<?php
/**
 * EventController — TanamPohon.id
 * 
 * Mengelola informasi kegiatan aksi penanaman pohon relawan,
 * pendaftaran relawan, penerbitan e-ticket dengan QR Code, dan bundle kaos relawan.
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/PlantingEvent.php';
require_once __DIR__ . '/../Models/Participant.php';
require_once __DIR__ . '/../Models/Ticket.php';

class EventController extends Controller
{
    private PlantingEvent $eventModel;
    private Participant $participantModel;
    private Ticket $ticketModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new PlantingEvent();
        $this->participantModel = new Participant();
        $this->ticketModel = new Ticket();
    }

    /**
     * Tampilkan daftar kegiatan aksi penanaman pohon relawan
     */
    public function index(): void
    {
        $filters = [
            'q'          => trim($_GET['q'] ?? ''),
            'status'     => $_GET['status'] ?? 'upcoming',
            'category'   => trim($_GET['kategori'] ?? ''),
            'difficulty' => trim($_GET['kesulitan'] ?? ''),
            'fee_type'   => trim($_GET['biaya'] ?? ''),
            'sort'       => $_GET['urut'] ?? 'date_asc',
            'view'       => $_GET['view'] ?? 'grid',
        ];

        try {
            $events        = $this->eventModel->getFilteredEvents($filters);
            $stats         = $this->eventModel->getExpeditionStats();
            $categories    = $this->eventModel->getCategories();
            $featuredEvent = $this->eventModel->getNextFeaturedExpedition();
        } catch (\Throwable $e) {
            error_log('[EventController] DB error: ' . $e->getMessage());
            $events = $stats = $categories = [];
            $featuredEvent = null;
        }

        $this->view('public/events/index', [
            'pageTitle'     => 'Jadwal Ekspedisi & Aksi Relawan Penanaman Pohon - ' . config('app.name'),
            'events'        => $events,
            'stats'         => $stats,
            'categories'    => $categories,
            'featuredEvent' => $featuredEvent,
            'filters'       => $filters,
        ]);
    }

    /**
     * Tampilkan detail kegiatan penanaman
     *
     * @param int $id ID event
     */
    public function show(int $id): void
    {
        $event = $this->eventModel->findById($id);

        if (!$event) {
            Session::flash('error', 'Kegiatan penanaman tidak ditemukan.');
            $this->redirect('/kegiatan');
            return;
        }

        $currentUser = auth();
        $userRegistered = false;

        if ($currentUser) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT id, registration_status 
                FROM participants 
                WHERE user_id = :user_id AND event_id = :event_id AND registration_status != 'batal'
                LIMIT 1
            ");
            $stmt->execute(['user_id' => $currentUser['id'], 'event_id' => $id]);
            $userRegistered = (bool)$stmt->fetch();
        }

        // Parse Rundown / Itinerary menjadi array terstruktur
        $itineraryItems = [];
        if (!empty($event['itinerary'])) {
            $lines = explode("\n", str_replace("\r", "", trim($event['itinerary'])));
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                if (str_contains($line, '|')) {
                    [$time, $act] = explode('|', $line, 2);
                    $itineraryItems[] = [
                        'time'     => trim($time),
                        'activity' => trim($act),
                    ];
                } else {
                    $itineraryItems[] = [
                        'time'     => '',
                        'activity' => $line,
                    ];
                }
            }
        }

        // Generate Google Calendar Link
        $startIso = date('Ymd\THis', strtotime($event['event_date'] . ' ' . $event['gathering_time']));
        $endIso   = date('Ymd\THis', strtotime($event['event_date'] . ' ' . $event['gathering_time'] . ' +6 hours'));
        $gcalUrl  = "https://calendar.google.com/calendar/render?action=TEMPLATE"
            . "&text=" . urlencode($event['title'])
            . "&dates=" . $startIso . "/" . $endIso
            . "&details=" . urlencode(strip_tags($event['description'] ?? ''))
            . "&location=" . urlencode(($event['gathering_point'] ?? '') . ', ' . ($event['location_name'] ?? ''));

        // Ekspedisi terkait lainnya
        $db = Database::getInstance()->getConnection();
        $relatedStmt = $db->prepare("
            SELECT e.*, l.name as location_name, l.city 
            FROM planting_events e
            LEFT JOIN planting_locations l ON e.location_id = l.id
            WHERE e.id != :id AND e.status = 'upcoming' AND e.event_date >= CURDATE()
            ORDER BY e.event_date ASC
            LIMIT 2
        ");
        $relatedStmt->execute([':id' => $id]);
        $relatedEvents = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('public/events/detail', [
            'pageTitle'      => e($event['title']) . ' - ' . config('app.name'),
            'event'          => $event,
            'currentUser'    => $currentUser,
            'userRegistered' => $userRegistered,
            'itineraryItems' => $itineraryItems,
            'gcalUrl'        => $gcalUrl,
            'relatedEvents'  => $relatedEvents,
        ]);
    }

    /**
     * Download kalender format iCalendar (.ics) untuk Apple / Google / Outlook
     *
     * @param int $id
     */
    public function downloadIcs(int $id): void
    {
        $event = $this->eventModel->findById($id);
        if (!$event) {
            $this->redirect('/kegiatan');
            return;
        }

        $dtStart = date('Ymd\THis', strtotime($event['event_date'] . ' ' . $event['gathering_time']));
        $dtEnd   = date('Ymd\THis', strtotime($event['event_date'] . ' ' . $event['gathering_time'] . ' +6 hours'));
        $now     = gmdate('Ymd\THis\Z');
        $uid     = 'event-' . $event['id'] . '-' . time() . '@tanampohon.id';
        $summary = preg_replace('/[[:cntrl:]]/', '', $event['title']);
        $location= preg_replace('/[[:cntrl:]]/', '', ($event['gathering_point'] ?? '') . ', ' . ($event['location_name'] ?? ''));
        $description = str_replace(["\r", "\n"], ["", "\\n"], strip_tags($event['description'] ?? ''));

        $ics = "BEGIN:VCALENDAR\r\n"
             . "VERSION:2.0\r\n"
             . "PRODID:-//TanamPohon.id//Planting Expedition Calendar//ID\r\n"
             . "CALSCALE:GREGORIAN\r\n"
             . "METHOD:PUBLISH\r\n"
             . "BEGIN:VEVENT\r\n"
             . "UID:{$uid}\r\n"
             . "DTSTAMP:{$now}\r\n"
             . "DTSTART;TZID=Asia/Jakarta:{$dtStart}\r\n"
             . "DTEND;TZID=Asia/Jakarta:{$dtEnd}\r\n"
             . "SUMMARY:{$summary}\r\n"
             . "DESCRIPTION:{$description}\r\n"
             . "LOCATION:{$location}\r\n"
             . "STATUS:CONFIRMED\r\n"
             . "URL:https://tanampohon.id/kegiatan/{$event['id']}\r\n"
             . "END:VEVENT\r\n"
             . "END:VCALENDAR\r\n";

        $filename = 'ekspedisi-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', strtolower($event['title'])) . '.ics';

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($ics));
        header('Cache-Control: no-cache, no-store, must-revalidate');

        echo $ics;
        exit;
    }

    /**
     * Proses pendaftaran relawan ke kegiatan penanaman
     *
     * @param int $id ID event
     */
    public function register(int $id): void
    {
        // Pendaftaran relawan mewajibkan akun agar e-ticket dapat dikelola di dashboard
        if (!Session::isLoggedIn()) {
            Session::set('intended_url', '/kegiatan/' . $id);
            Session::flash('warning', 'Silakan masuk atau daftar akun terlebih dahulu untuk mendaftar sebagai relawan.');
            $this->redirect('/masuk');
            return;
        }

        $event = $this->eventModel->findById($id);
        if (!$event || $event['status'] !== 'upcoming') {
            Session::flash('error', 'Kegiatan ini tidak membuka pendaftaran.');
            $this->redirect('/kegiatan');
            return;
        }

        $tshirtSize = $this->request->input('tshirt_size', 'L');
        $notes = $this->request->input('notes');

        $participantData = [
            'user_id'     => auth('id'),
            'tshirt_size' => $tshirtSize,
            'notes'       => $notes,
        ];

        try {
            $result = $this->participantModel->registerToEvent($participantData, $event);

            // Kirim notifikasi WhatsApp ke relawan via Fonnte
            try {
                require_once __DIR__ . '/../Services/FonnteService.php';
                $fonnte = new FonnteService();
                if ($result['is_paid'] && !empty($result['payment_id'])) {
                    $fonnte->notifyByPaymentId((int)$result['payment_id'], 'created');
                } elseif (!$result['is_paid'] && !empty($result['ticket_code'])) {
                    $user = auth();
                    if ($user) {
                        $fonnte->notifyEventConfirmed($user, $event, $result['ticket_code']);
                    }
                }
            } catch (\Throwable $te) {
                error_log('[Fonnte Notification Error] ' . $te->getMessage());
            }

            if ($result['is_paid']) {
                Session::flash('success', 'Pendaftaran berhasil dicatat! Silakan lakukan transfer biaya pendaftaran / bundle kaos sesuai petunjuk.');
                $this->redirect('/pembayaran/' . $result['invoice_no']);
            } else {
                Session::flash('success', 'Selamat! Pendaftaran Anda sebagai relawan telah terkonfirmasi. Simpan E-Ticket Anda berikut.');
                $this->redirect('/tiket/' . $result['ticket_code']);
            }
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('/kegiatan/' . $id);
        }
    }

    /**
     * Tampilkan E-Ticket relawan ber-QR code
     *
     * @param string $code Kode tiket
     */
    public function showTicket(string $code): void
    {
        $ticket = $this->ticketModel->findByCode($code);

        if (!$ticket) {
            Session::flash('error', 'Tiket tidak ditemukan atau kode tidak valid.');
            $this->redirect('/kegiatan');
            return;
        }

        $this->view('public/events/ticket', [
            'pageTitle' => 'E-Ticket: ' . e($ticket['ticket_code']),
            'ticket'    => $ticket,
        ]);
    }
}
