<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, shrink-to-fit=no">

        <!-- vendor CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://kit.fontawesome.com/0e68b45e64.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="<?= $asset ?>css/main.css">
        <link rel="stylesheet" href="<?= $asset ?>css/custom.css">
        <link rel="stylesheet" href="<?= $asset ?>css/template.css">
        <link rel="icon" href="<?= $asset ?>img/favicon.png">
        <title><?= TITLE . ' | '. $titre ?></title>
        <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        <!--<script src="https://www.google.com/recaptcha/api.js?render="></script>-->
    </head>
    <body class="bg-light" id="<?= $idPage ?>">
        <!-- Page Wrapper -->
        <div id="wrapper">

            <!-- Sidebar -->
            <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" >

                <!-- Sidebar - Brand -->
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= $routes->url(DEFAULT_PAGE); ?>">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <img src="https://img.icons8.com/cute-clipart/64/000000/bug.png" width="48" height="48" />
                    </div>
                    <div class="sidebar-brand-text mx-3">BugBounty</div>
                </a>

                <!-- Divider -->
                <hr class="sidebar-divider my-0">

                <!-- Nav Item - Dashboard -->
                <li class="nav-item active">
                    <a class="nav-link" href="<?= $routes->url('dashboard'); ?>">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span><?= $lang->getTxt('navbar', 'navitem-dashboard'); ?></span>
                    </a>
                </li>

                <!-- Nav Item - Platforms -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= $routes->url('dashboard'); ?>">
                        <i class="fas fa-pager"></i>
                        <span><?= $lang->getTxt('navbar', 'navitem-platforms'); ?></span>
                    </a>
                </li>

                <!-- Nav Item - Programs -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= $routes->url('dashboard'); ?>">
                        <i class="fas fa-file-alt"></i>
                        <span><?= $lang->getTxt('navbar', 'navitem-programs'); ?></span>
                    </a>
                </li>

                <!-- Nav Item - Reports -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= $routes->url('dashboard'); ?>">
                        <i class="fas fa-bug"></i>
                        <span><?= $lang->getTxt('navbar', 'navitem-reports'); ?></span>
                    </a>
                </li>

                <!-- Nav Item - Templates -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= $routes->url('dashboard'); ?>">
                        <i class="fas fa-clipboard"></i>
                        <span><?= $lang->getTxt('navbar', 'navitem-templates'); ?></span>
                    </a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">

                <!-- Nav Item - Profile -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= $routes->url('dashboard'); ?>">
                        <i class="fas fa-user"></i>
                        <span><?= $lang->getTxt('navbar', 'navitem-profile'); ?></span>
                    </a>
                </li>

                <!-- Nav Item - Settings -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= $routes->url('dashboard'); ?>">
                        <i class="fas fa-cog"></i>
                        <span><?= $lang->getTxt('navbar', 'navitem-settings'); ?></span>
                    </a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">

                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>
            </ul>
            <!-- End of Sidebar -->

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">

                <!-- Main Content -->
                <div id="content">

                    <!-- Topbar -->
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                        <!-- Sidebar Toggle (Topbar) -->
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>

                    </nav>
                    <!-- End of Topbar -->

                    <!-- Begin Page Content -->
                    <div class="container-fluid">
                        <?php if(isset($content) && !empty($content)){ echo $content; } ?>
                    </div>
                    <!-- End Page Content -->

                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; Lucas Martinelle 2020</span>
                        </div>
                    </div>
                </footer>
                <!-- End of Footer -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="<?= $asset ?>js/main.js"></script>
    </body>
</html>