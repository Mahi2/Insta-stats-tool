<?php
define('ROOT', realpath(__DIR__ . '/..') . '/');
require_once ROOT . 'core/includes/product.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="./assets/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./assets/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/favicons/favicon-16x16.png">
    <link rel="manifest" href="./assets/favicons/site.webmanifest">
    <link rel="mask-icon" href="./assets/favicons/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="./assets/favicons/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="./assets/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <title>Installation</title>
</head>
<body>

    <header class="header">
        <div class="container">
            <h1>Installation</h1>
            <p class="subheader d-flex flex-row">
                <span class="mr-3">For <a href="<?= PRODUCT_URL ?>"><?= PRODUCT_NAME ?></a></span>

                <span>🔥 <a href="https://altumcode.io/">AltumCode</a></span>
            </p>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <div class="row">

                <div class="col col-md-3 d-none d-md-block">

                    <nav class="nav sidebar-nav">
                        <ul class="sidebar" id="sidebar-ul">
                            <li class="nav-item">
                                <a href="#welcome" class="navigator nav-link">👁 Welcome</a>
                            </li>

                            <li class="nav-item">
                                <a href="#agreement" class="navigator nav-link" style="display: none">✉️ Agreement</a>
                            </li>

                            <li class="nav-item">
                                <a href="#requirements" class="navigator nav-link" style="display: none">💻 Requirements</a>
                            </li>

                            <li class="nav-item">
                                <a href="#setup" class="navigator nav-link" style="display: none">👨‍💻 Setup</a>
                            </li>

                            <li class="nav-item">
                                <a href="#finish" class="navigator nav-link" style="display: none">✅ Finish</a>
                            </li>
                        </ul>
                    </nav>

                </div>

                <div class="col" id="content">

                    <section id="welcome" style="display: none">
                        <h2>👁 Welcome</h2>
                        <p>The installation process should take less than <strong>5 minutes</strong> if you've done everything else from the documentation.</p>

                        <p>Thank you for choosing to use the product 💻.</p>

                        <a href="#agreement" id="welcome_start" class="navigator btn btn-altumcode">⚡️ Start the installation</a>
                    </section>


                    <section id="agreement" style="display: none">
                        <h2>✉️ Agreement</h2>
                        <p>Please make sure to read the agreement before moving forward.</p>

                        <div class="card">
                            <div class="card-body">
                                <strong>
                                    BY DOWNLOADING, INSTALLING, COPYING, ACCESSING OR USING THIS WEB APPLICATION, YOU AGREE TO THE TERMS OF THIS END USER LICENSE AGREEMENT. IF YOU ARE ACCEPTING THESE TERMS ON BEHALF OF ANOTHER PERSON OR COMPANY OR OTHER LEGAL ENTITY, YOU REPRESENT AND WARRANT THAT YOU HAVE FULL AUTHORITY TO BIND THAT PERSON, COMPANY OR LEGAL ENTITY TO THESE TERMS.
                                </strong>

                                <h3 class="mt-3">Installation</h3>
                                <p>The installation process of the product is straight forward and the steps that you need to take in order to install it are mentioned in the documentation of the product. It is your reponsibility to follow the guide and properly install the product as mentioned.</p>
                                <p>If you need installation support, please read the Installation Services section in the documentation and don't hesitate to contact me.</p>

                                <h3 class="mt-3">Customer Support</h3>
                                <p>Support is <strong>only</strong> done through Codecanyon's <a target="_blank" href="https://codecanyon.net/user/AltumCode">email form</a> or <strong>comments</strong> section.</p>

                                <p>Support does not mean any of the following:</p>

                                <ul>
                                    <li>Installation help</li>
                                    <li>Customization help</li>
                                    <li>Providing help for problems created while altering / modifying the product.</li>
                                </ul>

                                <p>When you start to modify and customize the product you are taking full responsibility for your own changes.</p>

                                <h3 class="mt-3">Data Collection</h3>
                                <p>When you install the product, the following data is collected and stored:</p>

                                <ol>
                                    <li>Purchase Code</li>
                                    <li>Website URL</li>
                                    <li>Server IP</li>
                                </ol>

                                <p>This data will never be sold or shown to anyone else but the Product Creator ( AltumCode ).</p>
                            </div>
                        </div>

                        <a href="#requirements" id="areement_agree" class="navigator btn btn-altumcode mt-3">✏️️ I agree</a>
                    </section>

                    <section id="requirements" style="display: none">
                        <?php $requirements = true ?>
                        <h2>💻 Requirements</h2>
                        <p>Make sure everything is checked so that you do not run into problems.</p>

                        <table class="table mt-3">
                            <thead class="thead-black">
                                <th></th>
                                <th>Required</th>
                                <th>Current</th>
                                <th></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>PHP Version</th>
                                    <td>7.0.0, or higher</td>
                                    <td><?= PHP_VERSION ?></td>
                                    <td>
                                        <?php if(version_compare(PHP_VERSION, '7.0.0') >= 0): ?>
                                            <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                        <?php else: ?>
                                            <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                            <?php $requirements = false ?>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>cURL</td>
                                    <td>Enabled</td>
                                    <td><?= function_exists('curl_version') ? 'Enabled' : 'Not Enabled' ?></td>
                                    <td>
                                        <?php if(function_exists('curl_version')): ?>
                                            <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                        <?php else: ?>
                                            <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                            <?php $requirements = false ?>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>OpenSSL</td>
                                    <td>Enabled</td>
                                    <td><?= extension_loaded('openssl') ? 'Enabled' : 'Not Enabled' ?></td>
                                    <td>
                                        <?php if(extension_loaded('openssl')): ?>
                                            <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                        <?php else: ?>
                                            <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                            <?php $requirements = false ?>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>mbstring</td>
                                    <td>Enabled</td>
                                    <td><?= extension_loaded('mbstring') && function_exists('mb_get_info') ? 'Enabled' : 'Not Enabled' ?></td>
                                    <td>
                                        <?php if(extension_loaded('mbstring') && function_exists('mb_get_info')): ?>
                                            <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                        <?php else: ?>
                                            <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                            <?php $requirements = false ?>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>MySQLi</td>
                                    <td>Enabled</td>
                                    <td><?= function_exists('mysqli_connect') ? 'Enabled' : 'Not Enabled' ?></td>
                                    <td>
                                        <?php if(function_exists('mysqli_connect')): ?>
                                            <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                        <?php else: ?>
                                            <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                            <?php $requirements = false ?>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table mt-3">
                            <thead class="thead-black">
                                <th>Path / File</th>
                                <th>Status</th>
                                <th></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>/uploads/favicon/</th>
                                    <td><?= is_writable(ROOT . 'uploads/favicon/') ? 'Writable' : 'Not Writable' ?></td>
                                    <td>
                                        <?php if(is_writable(ROOT . 'uploads/favicon/')): ?>
                                            <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                        <?php else: ?>
                                            <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                            <?php $requirements = false ?>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>/uploads/logo/</th>
                                    <td><?= is_writable(ROOT . 'uploads/logo/') ? 'Writable' : 'Not Writable' ?></td>
                                    <td>
                                        <?php if(is_writable(ROOT . 'uploads/logo/')): ?>
                                            <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                        <?php else: ?>
                                            <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                            <?php $requirements = false ?>
                                        <?php endif ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>/core/config/config.php</th>
                                    <td><?= is_writable(ROOT . '/core/config/config.php') ? 'Writable' : 'Not Writable' ?></td>
                                    <td>
                                        <?php if(is_writable(ROOT . '/core/config/config.php')): ?>
                                            <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                        <?php else: ?>
                                            <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                            <?php $requirements = false ?>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div>
                            <?php if($requirements): ?>
                                <a href="#setup" class="navigator btn btn-altumcode">🔥 Next</a>
                            <?php else: ?>
                                <p class="text-danger">Please make sure all the requirements listed on the documentation and on this page are met before continuing!</p>
                            <?php endif ?>
                        </div>
                    </section>

                    <section id="setup" style="display: none">
                        <h2>👨‍💻 Setup</h2>

                        <form id="setup_form" method="post" action="" role="form">
                            <div class="form-group">
                                <label for="license">Purchase Code*</label>
                                <input type="text" class="form-control" value="amazcode" id="license" name="license" aria-describedby="license_help" required="required">
                                <small id="license_help" class="form-text text-muted">The Purchase Code you got from purchasing the product.</small>
                            </div>

                            <h3 class="mt-5">Database Details</h3>
                            <p>Here are the connection details of the database that you want to use for this product.</p>

                            <div class="form-group row">
                                <label for="database_host" class="col-sm-2 col-form-label">Host*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="database_host" name="database_host" value="localhost" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="database_name" class="col-sm-2 col-form-label">Name*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="database_name" name="database_name" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="database_username" class="col-sm-2 col-form-label">Username*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="database_username" name="database_username" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="database_password" class="col-sm-2 col-form-label">Password*</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="database_password" name="database_password">
                                </div>
                            </div>

                            <h3 class="mt-5">General</h3>

                            <div class="form-group row">
                                <label for="url" class="col-sm-2 col-form-label">Website URL*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="url" name="url" placeholder="https://example.com/" aria-describedby="url_help" required="required">
                                    <small id="url_help" class="form-text text-muted">Make sure to specify the full url of the installation path of the product. Subdomain example: <code>https://subdomain.domain.com/</code> Subfolder example: <code>https://domain.com/product/</code></small>
                                </div>
                            </div>

                            <h3 class="mt-5">Get exclusive updates & discounts 🔥</h3>
                            <p>Sign up for the exclusive mail list for verified customers only. (optional)</p>
                            <p>I'm going to send you stuff like: <strong>exclusive discounts</strong>, <strong>updates</strong> and <strong>new products</strong>.</p>

                            <div class="form-group row">
                                <label for="client_email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="client_email" name="client_email" placeholder="Your valid email address">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="client_name" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="client_name" name="client_name" placeholder="Your name">
                                </div>
                            </div>

                            <button type="submit" name="submit" class="btn btn-altumcode mt-5">✅ Finish Installation</button>
                        </form>
                    </section>

                    <section id="finish" style="display: none">
                        <h2>✅ Installation Completed</h2>
                        <p class="text-success">Congratulations! The installation has been successful!</p>

                        <p class="mt-5">You can now login with the following information:</p>

                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>URL</th>
                                    <td><a href="" id="final_url"></a></td>
                                </tr>
                                <tr>
                                    <th>Username</th>
                                    <td>admin</td>
                                </tr>
                                <tr>
                                    <th>Password</th>
                                    <td>amazcode</td>
                                </tr>
                            </tbody>
                        </table>

                        <p class="mt-5 text-danger">Make sure to delete the <code>/install</code> folder!.</p>
                    </section>
                </div>

            </div>
        </div>
    </main>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>