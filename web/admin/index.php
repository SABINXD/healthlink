<?php
$function_url = "../assets/php/function.php";
include('./php/admin_functions.php');
if (!isset($_SESSION['admin_auth'])) header('Location:./pages/login.php');
$admin = getAdmin($_SESSION['admin_auth']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>healthlink | Doctor Verification</title>
  <link rel="shortcut icon" href="../assets/img/circle-icon.png" type="image/x-icon">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <!-- tailwind  -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <!-- gspa  -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

  <style>
    /* Custom scrollbar styling */
    .custom-scrollbar::-webkit-scrollbar {
      width: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #c5c5c5;
      border-radius: 10px;
      transition: background 0.2s ease;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }

    /* Modal styling */
    .modal-overlay {
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
    }

    /* Document thumbnail hover effect */
    .document-thumbnail {
      transition: all 0.3s ease;
    }

    .document-thumbnail:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Button hover effects */
    button {
      transition: all 0.2s ease;
    }

    button:hover {
      transform: translateY(-1px);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .mobile-stack {
        flex-direction: column;
      }

      .mobile-full-width {
        width: 100%;
      }
    }

    .verification-card {
      transition: all 0.3s ease;
      border-radius: 0.75rem;
    }

    .verification-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .status-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-weight: 600;
    }

    .action-button {
      transition: all 0.2s ease;
    }

    .action-button:hover {
      transform: scale(1.05);
    }

    .document-thumbnail {
      transition: all 0.3s ease;
      overflow: hidden;
    }

    .document-thumbnail:hover {
      transform: scale(1.03);
    }

    .document-thumbnail img {
      transition: all 0.3s ease;
    }

    .document-thumbnail:hover img {
      transform: scale(1.1);
    }

    .notification-toast {
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      border-left: 4px solid transparent;
    }

    .success-notification {
      border-left-color: #10B981;
    }

    .error-notification {
      border-left-color: #EF4444;
    }

    .info-notification {
      border-left-color: #3B82F6;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="../assets/img/logo.png" alt="AdminLTELogo" height="60" width="200">
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>
      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class=" btn btn-sm btn-danger" href="php/admin_actions.php?logout" role="button">
            Logout
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
      </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="../index.php" class="brand-link">
        <img src="../assets/img/logo.png" alt="AdminLTE Logo" height="45">
      </a>
      <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="info">
            <a href="#" class="d-block"><?= $admin['full_name'] ?></a>
          </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
              <a href="?dashboard" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="?edit_profile" class="nav-link">
                <i class="nav-icon fas fa-user"></i>
                <p>Edit Admin</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="?allposts" class="nav-link">
                <i class="nav-icon fas fa-book"></i>
                <p>All Posts</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="?doctorverification" class="nav-link <?= isset($_GET['doctorverification']) ? 'active' : '' ?>">
                <i class="nav-icon fas fa-user-md"></i>
                <p>Doctor Verification</p>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">
                <?php
                if (isset($_GET['edit_profile'])) {
                  echo "Edit Profile";
                } else if (isset($_GET['doctorverification'])) {
                  echo "Doctor Verification";
                } else {
                  echo "Dashboard";
                }
                ?>
              </h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right"></ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Main row -->
          <div class="row">
            <?php
            if (isset($_GET['edit_profile'])) {
            ?>
              <div class="card card-primary col-12">
                <div class="card-header">
                  <h3 class="card-title">Edit Your Profile</h3>
                </div>
                <!-- form start -->
                <?= showError('adminprofile') ?>
                <form method="post" action="php/admin_actions.php?updateprofile">
                  <input type="hidden" name="user_id" value="<?= $admin['id'] ?>">
                  <div class="card-body">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Full Name</label>
                      <input type="text" name="full_name" value="<?= $admin['full_name'] ?>" class="form-control" id="exampleInputEmail1" placeholder="Enter Full Name" required>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Email address</label>
                      <input type="email" name="email" value="<?= $admin['email'] ?>" class="form-control" id="exampleInputEmail1" placeholder="Enter email" required>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputPassword1">Password</label>
                      <input type="text" name="password" value="<?= $admin['password_text'] ?>" class="form-control" id="exampleInputPassword1" placeholder="Password">
                    </div>
                  </div>
                  <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                  </div>
                </form>
              </div>
            <?php
            } elseif (isset($_GET['doctorverification'])) {
              // Get verification requests from database
              $verificationRequests = getDoctorVerificationRequests();
              // Get verification statistics
              $stats = getDoctorVerificationStats();
            ?>
              <!-- Verification Stats Cards -->
              <div class="row mb-4">
                <div class="col-lg-3 col-6">
                  <div class="verification-card bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-lg shadow-lg">
                    <div class="flex items-center">
                      <div class="p-3 rounded-full bg-blue-400 bg-opacity-30">
                        <i class="fas fa-user-clock text-2xl"></i>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium">Pending</p>
                        <h3 class="text-2xl font-bold"><?= $stats['pending'] ?></h3>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="verification-card bg-gradient-to-r from-green-500 to-green-600 text-white p-4 rounded-lg shadow-lg">
                    <div class="flex items-center">
                      <div class="p-3 rounded-full bg-green-400 bg-opacity-30">
                        <i class="fas fa-user-check text-2xl"></i>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium">Approved</p>
                        <h3 class="text-2xl font-bold"><?= $stats['approved'] ?></h3>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="verification-card bg-gradient-to-r from-red-500 to-red-600 text-white p-4 rounded-lg shadow-lg">
                    <div class="flex items-center">
                      <div class="p-3 rounded-full bg-red-400 bg-opacity-30">
                        <i class="fas fa-user-times text-2xl"></i>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium">Declined</p>
                        <h3 class="text-2xl font-bold"><?= $stats['declined'] ?></h3>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-6">
                  <div class="verification-card bg-gradient-to-r from-purple-500 to-purple-600 text-white p-4 rounded-lg shadow-lg">
                    <div class="flex items-center">
                      <div class="p-3 rounded-full bg-purple-400 bg-opacity-30">
                        <i class="fas fa-users text-2xl"></i>
                      </div>
                      <div class="ml-4">
                        <p class="text-sm font-medium">Total</p>
                        <h3 class="text-2xl font-bold"><?= $stats['total'] ?></h3>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Verification Dashboard -->
              <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                  <div>
                    <h2 class="text-2xl font-bold text-gray-800">Doctor Verification Requests</h2>
                    <p class="text-gray-600 mt-1">Review and verify doctor credentials</p>
                  </div>
                  <div class="flex flex-wrap gap-2 mt-4 md:mt-0 mobile-stack">
                    <div class="relative">
                      <input type="text" id="searchInput" placeholder="Search requests..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                      <div class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-search"></i>
                      </div>
                    </div>
                    <select id="statusFilter" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                      <option value="all">All Statuses</option>
                      <option value="pending">Pending</option>
                      <option value="approved">Approved</option>
                      <option value="declined">Declined</option>
                    </select>
                    <button class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition flex items-center" id="exportBtn">
                      <i class="fas fa-download mr-2"></i>Export
                    </button>
                  </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="verificationTable">
                      <?php foreach ($verificationRequests as $request): ?>
                        <tr class="hover:bg-gray-50 transition-colors" data-status="<?= $request['status'] ?>">
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                              <div class="flex-shrink-0 h-12 w-12">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-lg">
                                  <?= substr($request['first_name'], 0, 1) ?>
                                </div>
                              </div>
                              <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900"><?= $request['first_name'] . ' ' . $request['last_name'] ?></div>
                                <div class="text-sm text-gray-500"><?= $request['email'] ?></div>
                              </div>
                            </div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $request['specialty'] ?></div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= date('M j, Y', strtotime($request['created_at'])) ?></div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $statusClass = '';
                            $statusText = '';
                            if ($request['status'] == 1) {  // Changed from 'approved' to 1
                              $statusClass = 'bg-green-100 text-green-800';
                              $statusText = 'Approved';
                            } elseif ($request['status'] == 2) {  // Changed from 'declined' to 2
                              $statusClass = 'bg-red-100 text-red-800';
                              $statusText = 'Declined';
                            } else {  // This will catch status 0 (pending)
                              $statusClass = 'bg-yellow-100 text-yellow-800';
                              $statusText = 'Pending';
                            }
                            ?>
                            <span class="status-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                              <?= $statusText ?>
                            </span>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3 view-details" data-id="<?= $request['id'] ?>"
                              data-json='<?= htmlspecialchars(json_encode($request)) ?>'>
                              <i class="fas fa-eye mr-1"></i>View Details
                            </button>
                            <?php
                            if ($request['status'] == 0) {
                            ?>
                              <form method="post" action="php/admin_actions.php?approve_doctor" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <button type="submit" class="text-green-600 hover:text-green-900 mr-3">
                                  <i class="fas fa-check mr-1"></i>Approve
                                </button>
                              </form>
                              <form method="post" action="php/admin_actions.php?decline_doctor" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                  <i class="fas fa-times mr-1"></i>Decline
                                </button>
                              </form>
                            <?php
                            }
                            ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-6">
                  <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">1</span> to <span class="font-medium"><?= count($verificationRequests) ?></span> of <span class="font-medium"><?= $stats['total'] ?></span> results
                  </div>
                  <div class="flex space-x-2">
                    <button class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50" id="prevPageBtn">
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="px-3 py-1 rounded-md bg-blue-600 text-white">1</button>
                    <button class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">2</button>
                    <button class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">3</button>
                    <button class="px-3 py-1 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200" id="nextPageBtn">
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Verification Details Modal -->
              <div id="detailsModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                  <!-- Background overlay -->
                  <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-overlay" aria-hidden="true"></div>

                  <!-- Modal container -->
                  <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <!-- Modal header -->
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b">
                      <div class="flex items-center justify-between">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">
                          Doctor Verification Details
                        </h3>
                        <button id="closeModal" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                          <span class="sr-only">Close</span>
                          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </button>
                      </div>
                    </div>

                    <!-- Modal body -->
                    <div class="bg-white px-4 py-5 sm:p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                      <!-- Verification Status Section -->
                      <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                          <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                          Verification Status
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-blue-50 p-5 rounded-lg shadow-sm">
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Current Status</p>
                            <span id="modalStatus" class="status-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full"></span>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Submission Date</p>
                            <p class="text-base font-semibold text-gray-800" id="modalSubmissionDate">-</p>
                          </div>
                          <div id="reviewDateContainer">
                            <p class="text-sm font-medium text-gray-500 mb-1">Review Date</p>
                            <p class="text-base font-semibold text-gray-800" id="modalReviewDate">-</p>
                          </div>
                        </div>
                      </div>

                      <!-- Personal Information Section -->
                      <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                          <i class="fas fa-user-circle mr-2 text-blue-500"></i>
                          Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-blue-50 p-5 rounded-lg shadow-sm">
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Full Name</p>
                            <p class="text-base font-semibold text-gray-800" id="modalFullName">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Other Name</p>
                            <p class="text-base font-semibold text-gray-800" id="modalOtherName">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Date of Birth</p>
                            <p class="text-base font-semibold text-gray-800" id="modalDateOfBirth">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Gender</p>
                            <p class="text-base font-semibold text-gray-800" id="modalGender">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Nationality</p>
                            <p class="text-base font-semibold text-gray-800" id="modalNationality">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Languages</p>
                            <p class="text-base font-semibold text-gray-800" id="modalLanguages">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Email</p>
                            <p class="text-base font-semibold text-gray-800" id="modalEmail">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Phone</p>
                            <p class="text-base font-semibold text-gray-800" id="modalPhone">-</p>
                          </div>
                          <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500 mb-1">Address</p>
                            <p class="text-base font-semibold text-gray-800" id="modalAddress">-</p>
                          </div>
                        </div>
                      </div>

                      <!-- Professional Information Section -->
                      <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                          <i class="fas fa-user-md mr-2 text-green-500"></i>
                          Professional Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-green-50 p-5 rounded-lg shadow-sm">
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Doctor Type</p>
                            <p class="text-base font-semibold text-gray-800" id="modalDoctorType">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Specialty</p>
                            <p class="text-base font-semibold text-gray-800" id="modalSpecialty">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Years of Experience</p>
                            <p class="text-base font-semibold text-gray-800" id="modalYearsOfExperience">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Medical School</p>
                            <p class="text-base font-semibold text-gray-800" id="modalMedicalSchool">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Graduation Year</p>
                            <p class="text-base font-semibold text-gray-800" id="modalGraduationYear">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Registration Number</p>
                            <p class="text-base font-semibold text-gray-800" id="modalRegistrationNumber">-</p>
                          </div>
                        </div>
                      </div>

                      <!-- NMC Verification Section -->
                      <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                          <i class="fas fa-certificate mr-2 text-purple-500"></i>
                          NMC Verification
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-purple-50 p-5 rounded-lg shadow-sm">
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">NMC Number</p>
                            <p class="text-base font-semibold text-gray-800" id="modalNmcNumber">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">NMC Issue Date</p>
                            <p class="text-base font-semibold text-gray-800" id="modalNmcIssueDate">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">NMC Expiry Date</p>
                            <p class="text-base font-semibold text-gray-800" id="modalNmcExpiryDate">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">NMC Status</p>
                            <p class="text-base font-semibold text-gray-800" id="modalNmcStatus">-</p>
                          </div>
                        </div>
                      </div>

                      <!-- Practice Information Section -->
                      <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                          <i class="fas fa-hospital mr-2 text-yellow-500"></i>
                          Practice Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-yellow-50 p-5 rounded-lg shadow-sm">
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Hospital/Clinic Name</p>
                            <p class="text-base font-semibold text-gray-800" id="modalHospitalName">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Department</p>
                            <p class="text-base font-semibold text-gray-800" id="modalDepartment">-</p>
                          </div>
                          <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500 mb-1">Hospital Address</p>
                            <p class="text-base font-semibold text-gray-800" id="modalHospitalAddress">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Hospital City</p>
                            <p class="text-base font-semibold text-gray-800" id="modalHospitalCity">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Hospital Phone</p>
                            <p class="text-base font-semibold text-gray-800" id="modalHospitalPhone">-</p>
                          </div>
                          <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Hospital Email</p>
                            <p class="text-base font-semibold text-gray-800" id="modalHospitalEmail">-</p>
                          </div>
                        </div>
                      </div>

                      <!-- Document Images Section -->
                      <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                          <i class="fas fa-file-alt mr-2 text-indigo-500"></i>
                          Submitted Documents
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="documentImages">
                          <!-- Document images will be inserted here by JavaScript -->
                        </div>
                      </div>

                      <!-- Verification Notes Section -->
                      <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                          <i class="fas fa-sticky-note mr-2 text-yellow-500"></i>
                          Verification Notes
                        </h3>
                        <textarea id="verificationNotes" class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none focus:border-transparent text-sm" rows="4" placeholder="Add any notes about this verification..."></textarea>
                      </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                      <button id="modalApproveBtn" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Approve
                      </button>
                      <button id="modalDeclineBtn" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Decline
                      </button>
                      <button id="modalCloseBtn" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Close
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Document Preview Modal -->
              <div id="documentPreviewModal" class="fixed inset-0 modal-overlay flex items-center justify-center hidden z-50 p-4">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg h-[85vh] flex flex-col">
                  <!-- Fixed Header -->
                  <div class="p-3 border-b flex justify-between items-center bg-white rounded-t-xl z-10">
                    <h3 class="text-base font-semibold text-gray-800" id="documentPreviewTitle">Document Preview</h3>
                    <button id="closeDocumentPreview" class="text-gray-500 hover:text-red-500 bg-gray-100 hover:bg-red-100 rounded-full p-2 transition-all duration-200 shadow-md">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                  <!-- Scrollable Content Area -->
                  <div class="flex-1 overflow-auto p-3 flex items-center justify-center bg-gray-100">
                    <img id="documentPreviewImage" src="" alt="Document Preview" class="max-w-full max-h-full object-contain">
                  </div>
                  <!-- Fixed Footer -->
                  <div class="p-3 border-t flex justify-end bg-white rounded-b-xl z-10">
                    <button id="downloadDocument" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center text-sm">
                      <i class="fas fa-download mr-2"></i>Download
                    </button>
                  </div>
                </div>
              </div>

              <!-- Notification Toast -->
              <div id="notification" class="fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 transform translate-y-20 transition-transform duration-300 max-w-md notification-toast hidden">
                <div class="flex items-center">
                  <div id="notificationIcon" class="mr-3 text-xl"></div>
                  <div>
                    <h4 class="font-semibold" id="notificationTitle">Notification</h4>
                    <p class="text-sm text-gray-600" id="notificationMessage">Action completed successfully</p>
                  </div>
                </div>
              </div>

              <script>
                // DOM elements
                const detailsModal = document.getElementById('detailsModal');
                const documentPreviewModal = document.getElementById('documentPreviewModal');
                const closeModalBtn = document.getElementById('closeModal');
                const closeDocumentPreviewBtn = document.getElementById('closeDocumentPreview');
                const modalApproveBtn = document.getElementById('modalApproveBtn');
                const modalDeclineBtn = document.getElementById('modalDeclineBtn');
                const downloadDocumentBtn = document.getElementById('downloadDocument');
                const notification = document.getElementById('notification');
                const searchInput = document.getElementById('searchInput');
                const documentPreviewImage = document.getElementById('documentPreviewImage');
                const documentPreviewTitle = document.getElementById('documentPreviewTitle');
                let currentRequest = null;
                let currentDocumentUrl = null;

                // Add event listeners to view-details buttons
                document.querySelectorAll('.view-details').forEach(button => {
                  button.addEventListener('click', function() {
                    const requestJson = this.getAttribute('data-json');
                    const request = JSON.parse(requestJson);
                    showDetailsModal(request);
                  });
                });

                // Status filter functionality
                document.getElementById('statusFilter').addEventListener('change', function() {
                  const selectedStatus = this.value;
                  const rows = document.querySelectorAll('#verificationTable tr');

                  rows.forEach(row => {
                    if (selectedStatus === 'all' || row.getAttribute('data-status') === selectedStatus) {
                      row.style.display = '';
                    } else {
                      row.style.display = 'none';
                    }
                  });
                });

                // Export functionality
                document.getElementById('exportBtn').addEventListener('click', function() {
                  // Create a temporary table with all data
                  const table = document.createElement('table');
                  const thead = document.createElement('thead');
                  const tbody = document.createElement('tbody');

                  // Copy header
                  const headerRow = document.querySelector('#verificationTable thead tr').cloneNode(true);
                  thead.appendChild(headerRow);

                  // Copy all rows (not just visible ones)
                  document.querySelectorAll('#verificationTable tr').forEach(row => {
                    tbody.appendChild(row.cloneNode(true));
                  });

                  table.appendChild(thead);
                  table.appendChild(tbody);

                  // Convert to CSV
                  let csv = [];
                  table.querySelectorAll('tr').forEach(row => {
                    const rowData = [];
                    row.querySelectorAll('td, th').forEach(cell => {
                      rowData.push('"' + cell.innerText.replace(/"/g, '""') + '"');
                    });
                    csv.push(rowData.join(','));
                  });

                  // Download CSV
                  const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
                  const encodedUri = encodeURI(csvContent);
                  const link = document.createElement('a');
                  link.setAttribute('href', encodedUri);
                  link.setAttribute('download', 'doctor_verification_requests.csv');
                  document.body.appendChild(link);
                  link.click();
                  document.body.removeChild(link);

                  showNotification('success', 'Export Successful', 'Verification requests have been exported as CSV.');
                });

                // Show details modal
                function showDetailsModal(request) {
                  currentRequest = request;

                  // Populate modal with request data
                  document.getElementById('modalTitle').innerHTML = `<i class="fas fa-user-md mr-2"></i> Doctor Verification - ${request.first_name} ${request.last_name}`;

                  // Personal Information
                  document.getElementById('modalFullName').textContent = request.first_name + ' ' + request.last_name || '-';
                  document.getElementById('modalOtherName').textContent = request.other_name || '-';
                  document.getElementById('modalDateOfBirth').textContent = request.date_of_birth ? new Date(request.date_of_birth).toLocaleDateString() : '-';
                  document.getElementById('modalGender').textContent = request.gender || '-';
                  document.getElementById('modalNationality').textContent = request.nationality || '-';
                  document.getElementById('modalLanguages').textContent = request.languages || '-';
                  document.getElementById('modalEmail').textContent = request.email || '-';
                  document.getElementById('modalPhone').textContent = request.phone || '-';
                  document.getElementById('modalAddress').textContent = request.address || '-';

                  // Professional Information
                  document.getElementById('modalDoctorType').textContent = request.doctor_type || '-';
                  document.getElementById('modalSpecialty').textContent = request.specialty || '-';
                  document.getElementById('modalYearsOfExperience').textContent = request.years_of_experience || '-';
                  document.getElementById('modalMedicalSchool').textContent = request.medical_school || '-';
                  document.getElementById('modalGraduationYear').textContent = request.graduation_year || '-';
                  document.getElementById('modalRegistrationNumber').textContent = request.registration_number || '-';

                  // NMC Verification
                  document.getElementById('modalNmcNumber').textContent = request.nmc_number || '-';
                  document.getElementById('modalNmcIssueDate').textContent = request.nmc_issue_date ? new Date(request.nmc_issue_date).toLocaleDateString() : '-';
                  document.getElementById('modalNmcExpiryDate').textContent = request.nmc_expiry_date ? new Date(request.nmc_expiry_date).toLocaleDateString() : '-';
                  document.getElementById('modalNmcStatus').textContent = request.nmc_status || '-';

                  // Practice Information
                  document.getElementById('modalHospitalName').textContent = request.hospital_name || '-';
                  document.getElementById('modalDepartment').textContent = request.department || '-';
                  document.getElementById('modalHospitalAddress').textContent = request.hospital_address || '-';
                  document.getElementById('modalHospitalCity').textContent = request.hospital_city || '-';
                  document.getElementById('modalHospitalPhone').textContent = request.hospital_phone || '-';
                  document.getElementById('modalHospitalEmail').textContent = request.hospital_email || '-';

                  // Verification Status
                  document.getElementById('modalSubmissionDate').textContent = request.created_at ? new Date(request.created_at).toLocaleDateString() : '-';

                  // Set status badge
                  const statusBadge = document.getElementById('modalStatus');
                  let statusClass = '';
                  let statusText = '';

                  if (request.status === 1) {
                    statusClass = 'bg-green-100 text-green-800';
                    statusText = 'Approved';
                  } else if (request.status === 2) {
                    statusClass = 'bg-red-100 text-red-800';
                    statusText = 'Declined';
                  } else {
                    statusClass = 'bg-yellow-100 text-yellow-800';
                    statusText = 'Pending';
                  }

                  statusBadge.className = `status-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}`;
                  statusBadge.textContent = statusText;

                  // In the showDetailsModal function, replace this section:
                  // Set review date if available
                  const reviewDateContainer = document.getElementById('reviewDateContainer');
                  const reviewDateElement = document.getElementById('modalReviewDate');
                  if (request.updated_at && request.status !== 0) {
                    reviewDateElement.textContent = new Date(request.updated_at).toLocaleDateString();
                    reviewDateContainer.style.display = 'block';
                  } else {
                    reviewDateContainer.style.display = 'none';
                  }

                  // Create documents array
                  const documents = [];
                  if (request.cert_front) {
                    documents.push({
                      type: "Medical Certificate Front",
                      url: "../assets/img/verifydoctor/" + request.cert_front
                    });
                  }
                  if (request.cert_back) {
                    documents.push({
                      type: "Medical Certificate Back",
                      url: "../assets/img/verifydoctor/" + request.cert_back
                    });
                  }
                  if (request.nmc_cert) {
                    documents.push({
                      type: "NMC Certificate",
                      url: "../assets/img/verifydoctor/" + request.nmc_cert
                    });
                  }
                  if (request.citizenship) {
                    documents.push({
                      type: "Citizenship Document",
                      url: "../assets/img/verifydoctor/" + request.citizenship
                    });
                  }

                  // Populate document images
                  const documentImages = document.getElementById('documentImages');
                  documentImages.innerHTML = '';

                  documents.forEach(doc => {
                    const docDiv = document.createElement('div');
                    docDiv.className = 'bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 document-thumbnail';
                    docDiv.innerHTML = `
                      <div class="overflow-hidden h-48 bg-gray-100">
                        <img src="${doc.url}" alt="${doc.type}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                      </div>
                      <div class="p-4">
                        <p class="text-sm font-medium text-gray-800 truncate">${doc.type}</p>
                        <div class="mt-3 flex justify-between items-center">
                          <button class="text-xs font-medium text-blue-600 hover:text-blue-800 view-document flex items-center" data-url="${doc.url}" data-type="${doc.type}">
                            <i class="fas fa-expand mr-1"></i> View
                          </button>
                          <button class="text-xs font-medium text-green-600 hover:text-green-800 download-document flex items-center" data-url="${doc.url}" data-type="${doc.type}">
                            <i class="fas fa-download mr-1"></i> Download
                          </button>
                        </div>
                      </div>
                    `;
                    documentImages.appendChild(docDiv);
                  });

                  // Add event listeners to document view buttons
                  document.querySelectorAll('.view-document').forEach(button => {
                    button.addEventListener('click', function() {
                      const url = this.getAttribute('data-url');
                      const type = this.getAttribute('data-type');
                      showDocumentPreview(url, type);
                    });
                  });

                  // Add event listeners to document download buttons
                  document.querySelectorAll('.download-document').forEach(button => {
                    button.addEventListener('click', function() {
                      const url = this.getAttribute('data-url');
                      const type = this.getAttribute('data-type');
                      downloadDocumentDirectly(url, type);
                    });
                  });

                  // Update the approve and decline buttons in the modal to submit forms
                  modalApproveBtn.onclick = function() {
                    const notes = document.getElementById('verificationNotes').value;
                    const form = document.createElement('form');
                    form.method = 'post';
                    form.action = 'php/admin_actions.php?approve_doctor';

                    const requestId = document.createElement('input');
                    requestId.type = 'hidden';
                    requestId.name = 'request_id';
                    requestId.value = request.id;
                    form.appendChild(requestId);

                    const notesInput = document.createElement('input');
                    notesInput.type = 'hidden';
                    notesInput.name = 'notes';
                    notesInput.value = notes;
                    form.appendChild(notesInput);

                    document.body.appendChild(form);
                    form.submit();
                  };

                  modalDeclineBtn.onclick = function() {
                    const notes = document.getElementById('verificationNotes').value;
                    const form = document.createElement('form');
                    form.method = 'post';
                    form.action = 'php/admin_actions.php?decline_doctor';

                    const requestId = document.createElement('input');
                    requestId.type = 'hidden';
                    requestId.name = 'request_id';
                    requestId.value = request.id;
                    form.appendChild(requestId);

                    const notesInput = document.createElement('input');
                    notesInput.type = 'hidden';
                    notesInput.name = 'notes';
                    notesInput.value = notes;
                    form.appendChild(notesInput);

                    document.body.appendChild(form);
                    form.submit();
                  };

                  // Show/hide action buttons based on status
                  if (request.status === 0) {
                    modalApproveBtn.style.display = 'inline-flex';
                    modalDeclineBtn.style.display = 'inline-flex';
                  } else {
                    modalApproveBtn.style.display = 'none';
                    modalDeclineBtn.style.display = 'none';
                  }

                  // Clear notes field
                  document.getElementById('verificationNotes').value = '';

                  // Show modal with animation
                  const modal = document.getElementById('detailsModal');
                  modal.classList.remove('hidden');

                  // Animate modal entrance
                  gsap.fromTo("#detailsModal .inline-block", {
                    opacity: 0,
                    scale: 0.95,
                    y: 20
                  }, {
                    opacity: 1,
                    scale: 1,
                    y: 0,
                    duration: 0.3,
                    ease: "power2.out"
                  });

                  // Animate background overlay
                  gsap.fromTo("#detailsModal .fixed.inset-0", {
                    opacity: 0
                  }, {
                    opacity: 1,
                    duration: 0.3
                  });
                }

                // Show document preview modal
                function showDocumentPreview(url, type) {
                  currentDocumentUrl = url;
                  documentPreviewImage.src = url;
                  documentPreviewTitle.textContent = type;
                  documentPreviewModal.classList.remove('hidden');
                  gsap.fromTo("#documentPreviewModal > div", {
                    scale: 0.9,
                    opacity: 0
                  }, {
                    scale: 1,
                    opacity: 1,
                    duration: 0.2,
                    ease: "back.out(1.2)"
                  });
                }

                // Close modal with animation
                function closeModal() {
                  gsap.to("#detailsModal .inline-block", {
                    opacity: 0,
                    scale: 0.95,
                    y: 20,
                    duration: 0.2,
                    ease: "power2.in",
                    onComplete: () => {
                      document.getElementById('detailsModal').classList.add('hidden');
                      currentRequest = null;
                    }
                  });
                  gsap.to("#detailsModal .fixed.inset-0", {
                    opacity: 0,
                    duration: 0.2
                  });
                }

                function closeDocumentPreview() {
                  gsap.to("#documentPreviewModal > div", {
                    scale: 0.9,
                    opacity: 0,
                    duration: 0.15,
                    ease: "power2.in",
                    onComplete: () => {
                      documentPreviewModal.classList.add('hidden');
                      currentDocumentUrl = null;
                    }
                  });
                }

                // Direct download function
                function downloadDocumentDirectly(url, type) {
                  const link = document.createElement('a');
                  link.href = url;
                  link.download = `${type.replace(/\s+/g, '_')}.jpg`;
                  document.body.appendChild(link);
                  link.click();
                  document.body.removeChild(link);
                  showNotification('success', 'Download Started', `${type} download has started.`);
                }

                // Show notification
                function showNotification(type, title, message) {
                  const notificationIcon = document.getElementById('notificationIcon');
                  const notificationTitle = document.getElementById('notificationTitle');
                  const notificationMessage = document.getElementById('notificationMessage');
                  const notificationEl = document.getElementById('notification');
                  // Reset classes
                  notificationEl.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 transform transition-transform duration-300 max-w-md notification-toast';
                  // Set icon and style based on type
                  if (type === 'success') {
                    notificationIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                    notificationEl.classList.add('success-notification');
                  } else if (type === 'error') {
                    notificationIcon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
                    notificationEl.classList.add('error-notification');
                  } else {
                    notificationIcon.innerHTML = '<i class="fas fa-info-circle text-blue-500"></i>';
                    notificationEl.classList.add('info-notification');
                  }
                  notificationTitle.textContent = title;
                  notificationMessage.textContent = message;
                  // Show notification
                  notificationEl.classList.remove('translate-y-20', 'hidden');
                  // Hide after 3 seconds
                  setTimeout(() => {
                    notificationEl.classList.add('translate-y-20');
                    setTimeout(() => {
                      notificationEl.classList.add('hidden');
                    }, 300);
                  }, 3000);
                }

                // Format date
                function formatDate(dateString) {
                  const options = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                  };
                  return new Date(dateString).toLocaleDateString(undefined, options);
                }

                // Search functionality
                searchInput.addEventListener('input', function() {
                  const searchTerm = this.value.toLowerCase();
                  const rows = document.querySelectorAll('#verificationTable tr');
                  for (let i = 0; i < rows.length; i++) {
                    const name = rows[i].querySelector('td:first-child')?.textContent.toLowerCase() || '';
                    const specialization = rows[i].querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                    if (name.includes(searchTerm) || specialization.includes(searchTerm)) {
                      rows[i].style.display = '';
                    } else {
                      rows[i].style.display = 'none';
                    }
                  }
                });

                // Event listeners
                closeModalBtn.addEventListener('click', closeModal);
                closeDocumentPreviewBtn.addEventListener('click', closeDocumentPreview);
                downloadDocumentBtn.addEventListener('click', function() {
                  if (currentDocumentUrl) {
                    downloadDocumentDirectly(currentDocumentUrl, 'Document');
                  }
                });

                // Close modals when clicking outside
                window.addEventListener('click', function(event) {
                  if (event.target === detailsModal) {
                    closeModal();
                  }
                  if (event.target === documentPreviewModal) {
                    closeDocumentPreview();
                  }
                });

                // Check for session notification and display it
                <?php if (isset($_SESSION['error']) && $_SESSION['error']['field'] == 'doctor_verification'): ?>
                  document.addEventListener('DOMContentLoaded', function() {
                    showNotification('<?= $_SESSION['error']['type'] ?>', 'Notification', '<?= $_SESSION['error']['msg'] ?>');
                  });
                <?php endif; ?>
              </script>
            <?php
            } elseif (isset($_GET['allposts'])) {
            ?>
              <div class="card card-primary col-12">
                <table class="table table-bordered table-hover">
                  <thead>
                    <?php
                    $posts = getPost();
                    $count = 1;
                    ?>
                    <tr>
                      <th>#No</th>
                      <th>User</th>
                      <th>post</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($posts as $post) {
                    ?>
                      <tr>
                        <td>#<?= $count ?></td>
                        <td>
                          <div class="d-flex">
                            <div>
                              <img src="../assets/img/profile/<?= $post['profile_pic'] ?>" class="rounded-circle border border-2 shadow-sm mx-2" width="55px" height="55px" />
                            </div>
                            <div>
                              <h5><?= $post['first_name'] . ' ' . $post['last_name'] ?> </h5>
                            </div>
                          </div>
                        </td>
                        <td>
                          <img class="w-100 h-100 object-fit-cover" style="aspect-ratio: 16/9;" src="../assets/img/posts/<?= $post['post_img'] ?>" alt="">
                        </td>
                        <td>
                          <button class="m-1 btn btn-danger btn-sm delete-post ub" data-post-id="<?= $post['id'] ?>">Delete</button>
                          <button class="m-1 btn btn-primary btn-sm warn-post" data-post-id="<?= $post['id'] ?>">Send Warning</button>
                        </td>
                      </tr>
                    <?php
                      $count++;
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            <?php
            } else {
              // Default dashboard view
            ?>
              <div class="row">
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-info">
                    <div class="inner">
                      <h3><?= totalUsersCount() ?></h3>
                      <p>Total Users</p>
                    </div>
                    <div class="icon">
                      <i class="nav-icon fas fa-user"></i>
                    </div>
                  </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-success">
                    <div class="inner">
                      <h3><?= totalPostsCount() ?></h3>
                      <p>Total Posts</p>
                    </div>
                    <div class="icon">
                      <i class="fa-solid fa-book"></i>
                    </div>
                  </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-warning">
                    <div class="inner">
                      <h3><?= totalCommentsCount() ?></h3>
                      <p>Total Comments</p>
                    </div>
                    <div class="icon">
                      <i class="fa-solid fa-comment"></i>
                    </div>
                  </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                  <!-- small box -->
                  <div class="small-box bg-danger">
                    <div class="inner">
                      <h3><?= totalLikesCount() ?></h3>
                      <p>Total Likes</p>
                    </div>
                    <div class="icon">
                      <i class="fa-solid fa-heart"></i>
                    </div>
                  </div>
                </div>
                <!-- ./col -->
              </div>
              <div class="card w-100">
                <div class="card-header">
                  <h3 class="card-title">User Lists</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <?php
                  $userslist = getUsersList();
                  $count = 1;
                  ?>
                  <table class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>#No</th>
                        <th>User</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      foreach ($userslist as $user) {
                      ?>
                        <tr>
                          <td>#<?= $count ?></td>
                          <td>
                            <div class="d-flex">
                              <div>
                                <img src="../assets/img/profile/<?= $user['profile_pic'] ?>" class="rounded-circle border border-2 shadow-sm mx-2" width="55px" height="55px" />
                              </div>
                              <div>
                                <h5><?= $user['first_name'] . ' ' . $user['last_name'] ?> - <span class="text-muted">@<?= $user['username'] ?></span></h5>
                                <h6 class="text-muted"><?= $user['email'] ?></h6>
                              </div>
                            </div>
                          </td>
                          <td>
                            <a href="./php/admin_actions.php?userlogin=<?= $user['email'] ?>" target="_blank" class="btn btn-success btn-sm m-1">Login User</a>
                            <?php if ($user['ac_status'] == 0): ?><button class="m-1 btn btn-warning btn-sm verify_user_btn" data-user-id="<?= $user['id'] ?>">Verify</button><?php endif; ?>
                            <button style="display:<?= $user['ac_status'] == 1 ? '' : 'none' ?>" class="m-1 btn btn-danger btn-sm block_user_btn ub" data-user-id="<?= $user['id'] ?>">Block</button>
                            <button style="display:<?= $user['ac_status'] == 2 ? '' : 'none' ?>" class="m-1 btn btn-primary btn-sm unblock_user_btn" data-user-id="<?= $user['id'] ?>">Unblock</button>
                          </td>
                        </tr>
                      <?php
                        $count++;
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            <?php
            }
            ?>
          </div>
          <!-- /.row (main row) -->
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
      <strong>All Right Reserved by Sabcraft.
        <div class="float-right d-none d-sm-inline-block">
          <b>Version</b> 3.1.0
        </div>
    </footer>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <!-- ChartJS -->
  <script src="plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="plugins/moment/moment.min.js"></script>
  <script src="plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="dist/js/demo.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="dist/js/pages/dashboard.js"></script>
  <script src="js/actions.js?v=<?= time() ?>"></script>
</body>

</html>
<?php
if (isset($_SESSION['error'])) {
  unset($_SESSION['error']);
}
?>