<?php
    

    require_once __DIR__ . "/../config/conn.php";
    require_once __DIR__ . "/../model/Superadmin.php";

    class SuperAdminController {
        private $model;

        public function __construct($conn){
            $this->model = new SuperAdminModel($conn);
        }

        public function getStats(){
            echo json_encode($this->model->getStats());
        }

        public function getAdmins() {
            echo json_encode($this->model->getAllAdmin());
        }

        public function createAdmin(){
            $data = $_POST;
            $data['registered_by'] = $_SESSION['super_admin_id'] ?? 0;

            echo json_encode($this->model->createAdmin($data));
        }

        public function deleteAdmin(){
            $input = json_decode(file_get_contents('php://input'), true);
            $success = $this->model->deleteAdmin($input['adminId']);

            echo json_encode(['success' => $success]);
        }

        public function toggleStatus() {
            $input = json_decode(file_get_contents('php://input'), true);
            $success = $this->model->toggleStatus($input['adminId'], $input['status']);
            echo json_encode(['success' => $success]);
        }

        public function getRequests() {
            echo json_encode($this->model->getPendingStaff());
        }

        public function handleRequest() {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input['staffId']) || empty($input['status'])) {
                echo json_encode(['success' => false, 'message' => 'Missing parameters']);
                return;
            }
            
            $success = $this->model->updateStaffStatus($input['staffId'], $input['status']);
            echo json_encode(['success' => $success]);
        }
    }

    //router
    $action = $_GET['action'] ?? '';

    $controller = new SuperAdminController($conn);

    match ($action) {
        'getStats'      => $controller->getStats(),
        'getAdmin'      => $controller->getAdmins(),
        'create'        => $controller->createAdmin(),
        'toggleStatus'  => $controller->toggleStatus(),
        'delete'        => $controller->deleteAdmin(),
        'getRequests'   => $controller->getRequests(),
        'handleRequest' => $controller->handleRequest(),
        default         => $controller->getStats()
    };
?>