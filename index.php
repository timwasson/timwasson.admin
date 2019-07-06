<?

session_start();

include("config.php");

$action = $_GET['action'];

function checkLoggedIn()
{
  if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    return true;
  } else {
    return false;
  }
}

/*
if (!checkLoggedIn() && $action != "login") {
  header("Location: ?action=login");
  die();
}
*/


?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>timwasson.com admin</title>
  <meta name="description" content="{{ description }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.css">
</head>

<body>
  <? if (checkLoggedIn()) { ?>
    <nav class="navbar" role="navigation" aria-label="main navigation">
      <div class="navbar-brand">
        <a class="navbar-item" href="https://bulma.io">
          <img src="https://bulma.io/images/bulma-logo.png" width="112" height="28">
        </a>

        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
      </div>

      <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-start">
          <a class="navbar-item" href="?action=list">
            List Entries
          </a>
          <a class="navbar-item" href="?action=add">
            New Entry
          </a>
        </div>

        <div class="navbar-end">
          <div class="navbar-item">
            <div class="buttons">
              <a class="button is-light" href="?action=logout">
                Log out
              </a>
            </div>
          </div>
        </div>
      </div>
    </nav>
  <? } ?>
  <section class="hero is-primary is-bold">
    <div class="hero-body">
      <div class="container">
        <h1 class="title">
          timwasson.com admin
        </h1>
        <h2 class="subtitle">
          hackers BEWARE
        </h2>
      </div>
    </div>
  </section>
  <div class="container">
    <div class="columns is-centered">
      <div class="column is-half-desktop">
        <?
        switch ($action) {
          case "list":

            // If logged in
            if (checkLoggedIn()) {
              foreach (glob($editdir . "*") as $file) {
                echo "<li><a href=\"?action=edit&file=" . basename($file) . "\">" . basename($file) . "</a></li>";
              }
            } else {
              echo "<script>location.href='?action=login';</script>";
            }

            break;
          case "edit":
          case "add":
            if($action == "edit") {
              $myfile = file_get_contents($editdir . $_GET["file"]);
              $filename = $_GET["file"];
              $nextAction = "save";
            } else {
              $myfile = "---
permalink: \"blog/[[ slug here]]\"
collection: blog
title: [[ TITLE ]]
date: " . date("Y-m-d") . "
layout: post.html.hbs
---\n";
              $filename = date("Y-m-d") . "-[[ clever-slug-here ]].md";
              $nextAction = "save-new";
            }
            ?>
            <form action="?action=save" method="post">
              <input class="input" name="filename" value="<?=$filename;?>" type="<? if ($action == "edit") { echo "hidden"; } else { echo "type"; } ?>">
              <textarea class="textarea" rows="20" name="contents"><?=$myfile; ?></textarea>
              <div class="control">
                <button class="button is-primary">Submit</button>
              </div>
            </form>
          <?
          break;
        case "login":
          ?>
          <form action="?action=auth" method="post">
            <div class="field">
              <div class="control">
                <input class="input is-large" name="password" type="password" placeholder="password">
              </div>
            </div>
            <div class="control">
              <button class="button is-primary">Submit</button>
            </div>
          </form>

          <?
          break;

        case "auth":
          if ($_POST['password'] == $password) {
            $_SESSION['loggedin'] = true;
            //echo "authenticated";
            echo "<script>location.href='?action=list';</script>";
          } else {
            $_SESSION['loggedin'] = false;
            echo "<div class=\"notification is-danger\">
            
            Wrong pass dummy.
          </div>";
          }
          break;

        case "logout":
          $_SESSION['loggedin'] = false;
          echo "<script>location.href='?action=login';</script>";
        break;

        case "save":
          if (checkLoggedIn()) {
            echo "filename: " . $_POST['filename'];
            file_put_contents($editdir . $_POST['filename'], $_POST['contents']);
            // Rebuild the site
            $output = exec($rebuildCmd);
            //$output = shell_exec('which node');
            echo "<pre>$output</pre>";
          }
        break;
      }
      ?>

        <footer class="footer">
          <div class="content has-text-centered">
            &copy; timwasson.com
          </div>
        </footer>
      </div>
    </div>
  </div>

</body>

</html>