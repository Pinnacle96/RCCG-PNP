<?php

namespace App\Controllers\Admin;

class CmsController extends \Controller {
    private array $modules;

    public function __construct() {
        parent::__construct();
        $this->modules = $this->moduleDefinitions();
    }

    public function index(): void {
        $this->requireAdmin();
        $module = $this->module();
        $config = $this->config($module);
        $search = trim((string) $this->input('q', ''));
        $page = max(1, (int) $this->input('page', 1));
        $limit = ADMIN_ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $where = $this->searchWhere($config, $search);
        $rows = \Database::fetchAll($this->selectSql($config, $where['sql'], $limit, $offset), $where['params']);
        $total = (int) \Database::fetchColumn('SELECT COUNT(*) FROM `' . $config['table'] . '` ' . $where['sql'], $where['params']);

        $this->view('admin.cms.index', [
            'title' => $config['title'],
            'page_title' => $config['title'],
            'module' => $module,
            'config' => $config,
            'rows' => $rows,
            'search' => $search,
            'page' => $page,
            'total' => $total,
            'totalPages' => max(1, (int) ceil($total / $limit)),
        ], 'admin');
    }

    public function create(): void {
        $this->requireAdmin();
        $module = $this->module();
        $config = $this->config($module);
        $this->assertWritable($config);

        $this->view('admin.cms.form', [
            'title' => 'Add ' . $config['singular'],
            'page_title' => 'Add ' . $config['singular'],
            'module' => $module,
            'config' => $config,
            'row' => null,
            'mode' => 'create',
            'options' => $this->options($config),
            'action' => BASE_URL . '/admin/' . $module,
        ], 'admin');
    }

    public function store(): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $module = $this->module();
        $config = $this->config($module);
        $this->assertWritable($config);
        $data = $this->payload($config);

        \Database::insert($config['table'], $data);
        $this->setFlash('success', $config['singular'] . ' created successfully.');
        $this->redirect(BASE_URL . '/admin/' . $module);
    }

    public function edit(): void {
        $this->requireAdmin();
        $module = $this->module();
        $config = $this->config($module);
        $this->assertWritable($config);
        $row = $this->findOr404($config);

        $this->view('admin.cms.form', [
            'title' => 'Edit ' . $config['singular'],
            'page_title' => 'Edit ' . $config['singular'],
            'module' => $module,
            'config' => $config,
            'row' => $row,
            'mode' => 'edit',
            'options' => $this->options($config),
            'action' => BASE_URL . '/admin/' . $module . '/edit/' . $row['id'],
        ], 'admin');
    }

    public function update(): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $module = $this->module();
        $config = $this->config($module);
        $this->assertWritable($config);
        $row = $this->findOr404($config);
        $data = $this->payload($config, (int) $row['id']);

        \Database::update($config['table'], $data, 'id = :id', [':id' => $row['id']]);
        $this->setFlash('success', $config['singular'] . ' updated successfully.');
        $this->redirect(BASE_URL . '/admin/' . $module);
    }

    public function delete(): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $module = $this->module();
        $config = $this->config($module);
        $this->assertWritable($config);
        $row = $this->findOr404($config);

        \Database::delete($config['table'], 'id = :id', [':id' => $row['id']]);
        $this->setFlash('success', $config['singular'] . ' deleted successfully.');
        $this->redirect(BASE_URL . '/admin/' . $module);
    }

    private function requireAdmin(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR, ROLE_DEACON]);
    }

    private function module(): string {
        return (string) ($_GET['module'] ?? '');
    }

    private function config(string $module): array {
        if (!isset($this->modules[$module])) {
            http_response_code(404);
            $this->view('frontend.404', ['title' => 'Admin Module Not Found'], 'public');
            exit;
        }
        return $this->modules[$module];
    }

    private function assertWritable(array $config): void {
        if (!empty($config['readonly'])) {
            $this->setFlash('error', $config['singular'] . ' records are managed from their source workflow.');
            $this->redirect(BASE_URL . '/admin/' . $this->module());
        }
    }

    private function findOr404(array $config): array {
        $id = (int) ($_GET['id'] ?? 0);
        $row = $id > 0 ? \Database::fetchOne('SELECT * FROM `' . $config['table'] . '` WHERE id = ? LIMIT 1', [$id]) : null;
        if (!$row) {
            http_response_code(404);
            $this->view('frontend.404', ['title' => $config['singular'] . ' Not Found'], 'public');
            exit;
        }
        return $row;
    }

    private function payload(array $config, ?int $id = null): array {
        $data = [];
        foreach ($config['fields'] as $name => $field) {
            $type = $field['type'] ?? 'text';
            if ($type === 'checkbox') {
                $data[$name] = $this->input($name) ? 1 : 0;
                continue;
            }

            if ($type === 'upload') {
                $uploaded = $this->handleUpload($name, $field, $id);
                if ($uploaded !== null) {
                    $data[$name] = $uploaded;
                } elseif (($field['required'] ?? false) && $id === null) {
                    $this->setFlash('error', $field['label'] . ' is required.');
                    $this->redirect($this->backToForm($id));
                }
                // No new file on edit (or optional on create): preserve existing value by omitting the key.
                continue;
            }

            $value = trim((string) $this->input($name, ''));
            if (($field['required'] ?? false) && $value === '') {
                $this->setFlash('error', $field['label'] . ' is required.');
                $this->redirect($this->backToForm($id));
            }
            if ($type === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->setFlash('error', $field['label'] . ' must be a valid email address.');
                $this->redirect($this->backToForm($id));
            }
            if ($type === 'number' && $value !== '') {
                $data[$name] = (float) $value;
                continue;
            }
            if ($type === 'select' && $value === '') {
                $data[$name] = null;
                continue;
            }
            $data[$name] = $value === '' && empty($field['required']) ? null : $value;
        }

        if (!empty($config['slug_from'])) {
            $source = (string) ($data[$config['slug_from']] ?? '');
            if ($source !== '') {
                $data['slug'] = \Helpers::uniqueSlug($source, $config['table'], 'slug', $id ? (string) $id : null);
            }
        }
        if (!empty($config['created_by']) && $id === null) {
            $data[$config['created_by']] = $this->userId;
        }
        if (($config['table'] ?? '') === 'blog_posts' && !empty($data['is_published']) && empty($data['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        if (($config['table'] ?? '') === 'contacts' && !empty($data['is_replied'])) {
            $data['replied_by'] = $this->userId;
            $data['replied_at'] = date('Y-m-d H:i:s');
        }

        return $data;
    }

    /**
     * Handle a file upload field. Returns the stored filename, or null when no
     * file was submitted. On a validation failure it flashes and redirects.
     */
    private function handleUpload(string $field, array $fieldConfig, ?int $id): ?string {
        $error = $_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE;
        if (empty($_FILES[$field]['name']) || $error === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $accept = $fieldConfig['accept'] ?? 'image';
        if ($accept === 'audio') {
            $allowed = ALLOWED_AUDIO_TYPES;
            $max = MAX_AUDIO_SIZE;
        } elseif ($accept === 'doc') {
            $allowed = ALLOWED_DOC_TYPES;
            $max = MAX_DOC_SIZE;
        } else {
            $allowed = ALLOWED_IMAGE_TYPES;
            $max = MAX_IMAGE_SIZE;
        }

        $uploader = new \Uploader(UPLOAD_PATH, $allowed, $max);
        $stored = $uploader->upload($field, $fieldConfig['subdir'] ?? '');
        if ($stored === null) {
            $this->setFlash('error', $fieldConfig['label'] . ': ' . implode(' ', $uploader->getErrors()));
            $this->redirect($this->backToForm($id));
        }
        return $stored;
    }

    private function backToForm(?int $id): string {
        $module = $this->module();
        return $id ? BASE_URL . '/admin/' . $module . '/edit/' . $id : BASE_URL . '/admin/' . $module . '/add';
    }

    private function searchWhere(array $config, string $search): array {
        if ($search === '' || empty($config['search'])) {
            return ['sql' => '', 'params' => []];
        }
        $clauses = [];
        $params = [];
        foreach ($config['search'] as $column) {
            $clauses[] = '`' . $column . '` LIKE ?';
            $params[] = '%' . $search . '%';
        }
        return ['sql' => 'WHERE ' . implode(' OR ', $clauses), 'params' => $params];
    }

    private function selectSql(array $config, string $where, int $limit, int $offset): string {
        $order = $config['order'] ?? 'id DESC';
        return 'SELECT * FROM `' . $config['table'] . '` ' . $where . ' ORDER BY ' . $order . ' LIMIT ' . $limit . ' OFFSET ' . $offset;
    }

    private function options(array $config): array {
        $options = [];
        foreach ($config['fields'] as $name => $field) {
            if (($field['type'] ?? '') !== 'select' || empty($field['source'])) {
                continue;
            }
            $source = $field['source'];
            if ($source === 'sermon_series') {
                $options[$name] = \Database::fetchAll('SELECT id AS value, title AS label FROM sermon_series ORDER BY title');
            }
            if ($source === 'events') {
                $options[$name] = \Database::fetchAll('SELECT id AS value, title AS label FROM events ORDER BY event_date DESC');
            }
            if ($source === 'gallery_albums') {
                $options[$name] = \Database::fetchAll('SELECT id AS value, title AS label FROM gallery_albums ORDER BY title');
            }
            if ($source === 'members') {
                $options[$name] = \Database::fetchAll("SELECT id AS value, CONCAT(first_name, ' ', last_name) AS label FROM members ORDER BY first_name, last_name");
            }
        }
        return $options;
    }

    private function moduleDefinitions(): array {
        return [
            'sermons' => [
                'title' => 'Sermons',
                'singular' => 'Sermon',
                'table' => 'sermons',
                'slug_from' => 'title',
                'created_by' => 'created_by',
                'order' => 'sermon_date DESC, id DESC',
                'search' => ['title', 'preacher', 'scripture_ref'],
                'columns' => ['title' => 'Title', 'preacher' => 'Preacher', 'sermon_date' => 'Date', 'sermon_type' => 'Type', 'is_published' => 'Published'],
                'fields' => [
                    'title' => ['label' => 'Title', 'required' => true],
                    'preacher' => ['label' => 'Preacher', 'required' => true],
                    'scripture_ref' => ['label' => 'Scripture Reference'],
                    'series_id' => ['label' => 'Series', 'type' => 'select', 'source' => 'sermon_series'],
                    'description' => ['label' => 'Description', 'type' => 'textarea'],
                    'audio_file' => ['label' => 'Audio File (mp3/m4a)', 'type' => 'upload', 'subdir' => 'sermons/', 'accept' => 'audio'],
                    'video_url' => ['label' => 'Video URL'],
                    'thumbnail' => ['label' => 'Thumbnail', 'type' => 'upload', 'subdir' => 'sermons/', 'accept' => 'image'],
                    'sermon_date' => ['label' => 'Sermon Date', 'type' => 'date', 'required' => true],
                    'sermon_type' => ['label' => 'Sermon Type', 'type' => 'select', 'options' => ['sunday' => 'Sunday', 'midweek' => 'Midweek', 'special' => 'Special', 'program' => 'Program']],
                    'duration_mins' => ['label' => 'Duration Minutes', 'type' => 'number'],
                    'tags' => ['label' => 'Tags'],
                    'is_featured' => ['label' => 'Featured', 'type' => 'checkbox'],
                    'is_published' => ['label' => 'Published', 'type' => 'checkbox'],
                ],
            ],
            'sermon-series' => [
                'title' => 'Sermon Series',
                'singular' => 'Sermon Series',
                'table' => 'sermon_series',
                'slug_from' => 'title',
                'order' => 'start_date DESC, id DESC',
                'search' => ['title', 'description'],
                'columns' => ['title' => 'Title', 'start_date' => 'Start', 'end_date' => 'End', 'is_active' => 'Active'],
                'fields' => [
                    'title' => ['label' => 'Title', 'required' => true],
                    'description' => ['label' => 'Description', 'type' => 'textarea'],
                    'cover_image' => ['label' => 'Cover Image', 'type' => 'upload', 'subdir' => 'sermons/', 'accept' => 'image'],
                    'start_date' => ['label' => 'Start Date', 'type' => 'date'],
                    'end_date' => ['label' => 'End Date', 'type' => 'date'],
                    'is_active' => ['label' => 'Active', 'type' => 'checkbox'],
                ],
            ],
            'events' => [
                'title' => 'Events',
                'singular' => 'Event',
                'table' => 'events',
                'slug_from' => 'title',
                'created_by' => 'created_by',
                'order' => 'event_date DESC, id DESC',
                'search' => ['title', 'venue', 'short_description'],
                'columns' => ['title' => 'Title', 'event_date' => 'Date', 'venue' => 'Venue', 'requires_registration' => 'Registration', 'is_published' => 'Published'],
                'fields' => [
                    'title' => ['label' => 'Title', 'required' => true],
                    'short_description' => ['label' => 'Short Description', 'type' => 'textarea'],
                    'description' => ['label' => 'Description', 'type' => 'textarea'],
                    'banner_image' => ['label' => 'Banner Image', 'type' => 'upload', 'subdir' => 'events/', 'accept' => 'image'],
                    'event_date' => ['label' => 'Event Date', 'type' => 'date', 'required' => true],
                    'end_date' => ['label' => 'End Date', 'type' => 'date'],
                    'start_time' => ['label' => 'Start Time', 'type' => 'time'],
                    'end_time' => ['label' => 'End Time', 'type' => 'time'],
                    'venue' => ['label' => 'Venue'],
                    'address' => ['label' => 'Address', 'type' => 'textarea'],
                    'registration_limit' => ['label' => 'Registration Limit', 'type' => 'number'],
                    'requires_registration' => ['label' => 'Requires Registration', 'type' => 'checkbox'],
                    'is_featured' => ['label' => 'Featured', 'type' => 'checkbox'],
                    'is_published' => ['label' => 'Published', 'type' => 'checkbox'],
                ],
            ],
            'event-registrations' => [
                'title' => 'Event Registrations',
                'singular' => 'Event Registration',
                'table' => 'event_registrations',
                'order' => 'created_at DESC',
                'search' => ['name', 'email', 'phone'],
                'columns' => ['event_id' => 'Event ID', 'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'status' => 'Status', 'created_at' => 'Registered'],
                'fields' => [
                    'event_id' => ['label' => 'Event', 'type' => 'select', 'source' => 'events', 'required' => true],
                    'name' => ['label' => 'Name', 'required' => true],
                    'email' => ['label' => 'Email', 'type' => 'email'],
                    'phone' => ['label' => 'Phone'],
                    'adults' => ['label' => 'Adults', 'type' => 'number'],
                    'children' => ['label' => 'Children', 'type' => 'number'],
                    'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled']],
                ],
            ],
            'ministries' => [
                'title' => 'Ministries',
                'singular' => 'Ministry',
                'table' => 'ministries',
                'slug_from' => 'name',
                'order' => 'display_order ASC, name ASC',
                'search' => ['name', 'leader_name', 'short_desc'],
                'columns' => ['name' => 'Name', 'leader_name' => 'Leader', 'meeting_schedule' => 'Schedule', 'is_active' => 'Active'],
                'fields' => [
                    'name' => ['label' => 'Name', 'required' => true],
                    'short_desc' => ['label' => 'Short Description', 'type' => 'textarea'],
                    'description' => ['label' => 'Description', 'type' => 'textarea'],
                    'leader_name' => ['label' => 'Leader Name'],
                    'leader_id' => ['label' => 'Leader Member', 'type' => 'select', 'source' => 'members'],
                    'meeting_schedule' => ['label' => 'Meeting Schedule'],
                    'meeting_venue' => ['label' => 'Meeting Venue'],
                    'contact_email' => ['label' => 'Contact Email', 'type' => 'email'],
                    'contact_phone' => ['label' => 'Contact Phone'],
                    'cover_image' => ['label' => 'Cover Image', 'type' => 'upload', 'subdir' => 'ministries/', 'accept' => 'image'],
                    'display_order' => ['label' => 'Display Order', 'type' => 'number'],
                    'is_active' => ['label' => 'Active', 'type' => 'checkbox'],
                ],
            ],
            'cell-groups' => [
                'title' => 'Cell Groups',
                'singular' => 'Cell Group',
                'table' => 'cell_groups',
                'order' => 'name ASC',
                'search' => ['name', 'zone', 'meeting_venue'],
                'columns' => ['name' => 'Name', 'zone' => 'Zone', 'meeting_day' => 'Day', 'meeting_venue' => 'Venue', 'is_active' => 'Active'],
                'fields' => [
                    'name' => ['label' => 'Name', 'required' => true],
                    'zone' => ['label' => 'Zone'],
                    'leader_id' => ['label' => 'Leader', 'type' => 'select', 'source' => 'members'],
                    'co_leader_id' => ['label' => 'Co-Leader', 'type' => 'select', 'source' => 'members'],
                    'meeting_day' => ['label' => 'Meeting Day', 'type' => 'select', 'options' => ['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday']],
                    'meeting_time' => ['label' => 'Meeting Time', 'type' => 'time'],
                    'meeting_venue' => ['label' => 'Meeting Venue'],
                    'is_active' => ['label' => 'Active', 'type' => 'checkbox'],
                ],
            ],
            'blog' => [
                'title' => 'Blog',
                'singular' => 'Blog Post',
                'table' => 'blog_posts',
                'slug_from' => 'title',
                'created_by' => 'author_id',
                'order' => 'created_at DESC',
                'search' => ['title', 'excerpt', 'tags'],
                'columns' => ['title' => 'Title', 'published_at' => 'Published', 'is_featured' => 'Featured', 'is_published' => 'Published'],
                'fields' => [
                    'title' => ['label' => 'Title', 'required' => true],
                    'excerpt' => ['label' => 'Excerpt', 'type' => 'textarea'],
                    'body' => ['label' => 'Body', 'type' => 'richtext', 'required' => true],
                    'cover_image' => ['label' => 'Cover Image', 'type' => 'upload', 'subdir' => 'blog/', 'accept' => 'image'],
                    'tags' => ['label' => 'Tags'],
                    'published_at' => ['label' => 'Published At', 'type' => 'datetime-local'],
                    'is_featured' => ['label' => 'Featured', 'type' => 'checkbox'],
                    'is_published' => ['label' => 'Published', 'type' => 'checkbox'],
                ],
            ],
            'gallery-albums' => [
                'title' => 'Gallery Albums',
                'singular' => 'Gallery Album',
                'table' => 'gallery_albums',
                'slug_from' => 'title',
                'order' => 'event_date DESC, id DESC',
                'search' => ['title', 'description'],
                'columns' => ['title' => 'Title', 'event_date' => 'Event Date', 'is_published' => 'Published'],
                'fields' => [
                    'title' => ['label' => 'Title', 'required' => true],
                    'description' => ['label' => 'Description', 'type' => 'textarea'],
                    'cover_image' => ['label' => 'Cover Image', 'type' => 'upload', 'subdir' => 'gallery/', 'accept' => 'image'],
                    'event_date' => ['label' => 'Event Date', 'type' => 'date'],
                    'is_published' => ['label' => 'Published', 'type' => 'checkbox'],
                ],
            ],
            'gallery' => [
                'title' => 'Gallery Items',
                'singular' => 'Gallery Item',
                'table' => 'gallery',
                'created_by' => 'created_by',
                'order' => 'album_id ASC, sort_order ASC, id DESC',
                'search' => ['title', 'file_path'],
                'columns' => ['album_id' => 'Album ID', 'title' => 'Title', 'file_type' => 'Type', 'sort_order' => 'Order', 'is_featured' => 'Featured'],
                'fields' => [
                    'album_id' => ['label' => 'Album', 'type' => 'select', 'source' => 'gallery_albums', 'required' => true],
                    'title' => ['label' => 'Title'],
                    'file_path' => ['label' => 'Image File', 'type' => 'upload', 'subdir' => 'gallery/', 'accept' => 'image', 'required' => true],
                    'file_type' => ['label' => 'File Type', 'type' => 'select', 'options' => ['image' => 'Image', 'video' => 'Video']],
                    'sort_order' => ['label' => 'Sort Order', 'type' => 'number'],
                    'is_featured' => ['label' => 'Featured', 'type' => 'checkbox'],
                ],
            ],
            'announcements' => [
                'title' => 'Announcements',
                'singular' => 'Announcement',
                'table' => 'announcements',
                'created_by' => 'created_by',
                'order' => 'start_date DESC, id DESC',
                'search' => ['title', 'body'],
                'columns' => ['title' => 'Title', 'type' => 'Type', 'target' => 'Target', 'start_date' => 'Start', 'is_active' => 'Active'],
                'fields' => [
                    'title' => ['label' => 'Title', 'required' => true],
                    'body' => ['label' => 'Body', 'type' => 'textarea', 'required' => true],
                    'type' => ['label' => 'Type', 'type' => 'select', 'options' => ['general' => 'General', 'urgent' => 'Urgent', 'event' => 'Event', 'pastoral' => 'Pastoral']],
                    'target' => ['label' => 'Target', 'type' => 'select', 'options' => ['all' => 'All', 'members' => 'Members', 'workers' => 'Workers', 'leaders' => 'Leaders']],
                    'start_date' => ['label' => 'Start Date', 'type' => 'date', 'required' => true],
                    'end_date' => ['label' => 'End Date', 'type' => 'date'],
                    'is_active' => ['label' => 'Active', 'type' => 'checkbox'],
                    'show_on_website' => ['label' => 'Show on Website', 'type' => 'checkbox'],
                ],
            ],
            'contacts' => [
                'title' => 'Contact Inbox',
                'singular' => 'Contact Message',
                'table' => 'contacts',
                'order' => 'created_at DESC',
                'search' => ['name', 'email', 'subject', 'message'],
                'columns' => ['name' => 'Name', 'email' => 'Email', 'subject' => 'Subject', 'is_read' => 'Read', 'is_replied' => 'Replied', 'created_at' => 'Received'],
                'fields' => [
                    'name' => ['label' => 'Name', 'required' => true],
                    'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
                    'phone' => ['label' => 'Phone'],
                    'subject' => ['label' => 'Subject', 'required' => true],
                    'message' => ['label' => 'Message', 'type' => 'textarea', 'required' => true],
                    'reply_text' => ['label' => 'Reply Notes', 'type' => 'textarea'],
                    'is_read' => ['label' => 'Read', 'type' => 'checkbox'],
                    'is_replied' => ['label' => 'Replied', 'type' => 'checkbox'],
                ],
            ],
        ];
    }
}
