<?php
// Shows users accounts, and allows user to create new account
header("Content-Type: text/html; charset=utf-8");

$html = file_get_contents("accounts.html");
$html_pieces = explode("<!--===entries===-->", $html);

$env = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($env as $line) {
    // Skip comments
    if (strpos(trim($line), '#') === 0) continue;

    // Split on the first '='
    [$key, $value] = explode('=', $line, 2);
    // Put in $_ENV
    $_ENV[trim($key)] = trim($value);
}

$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$db = $_ENV['DB_NAME'];

$holder_id = "";
$holder_name = "";
$password = "";

session_start();

// Establishing database connection
$conn = mysqli_connect($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sets the name and password when the user has logged in to their account
    if (isset($_POST["login"])) {
        $holder_name = strip_tags($_POST["name"]);
        $password = strip_tags($_POST["password"]);
        $_SESSION['holder_name'] = $holder_name;
        $_SESSION['password'] = $password;
        // Handles when user creates a new account holder
    } else if (isset($_POST["create_account"])) {
        $holder_name = strip_tags($_POST["name"]);
        $password = strip_tags($_POST["password"]);
        $_SESSION['holder_name'] = $holder_name;
        $_SESSION['password'] = $password;
        // Gets existing account holders that already have the password the user put in
        $login_statement = $conn->prepare("SELECT * FROM account_holders WHERE password = ?");
        $login_statement->bind_param("s", $password);
        $login_statement->execute();
        $rows = $login_statement->get_result();
        $login_statement->close();
        // Checks if the password is already used by another account holder, and if so sends user back to login page
        if ($rows->num_rows > 0) {
            header("Location: login.html");
            exit;
        }
        // Creates the new account holder with protection from SQL injection
        $create_statement = $conn->prepare("INSERT INTO account_holders (name, password) VALUES (?, ?)");
        $create_statement->bind_param("ss", $holder_name, $password);
        $create_statement->execute();
        $create_statement->close();
    }
}

// Sets account_holder's name and password via session to insure they always exist when reloading the page
if (isset($_SESSION['holder_name']) && isset($_SESSION['password'])) {
    $holder_name = $_SESSION['holder_name'];
    $password = $_SESSION['password'];
} else {
    header("Location: login.html");
    exit;
}

echo $html_pieces[0];

// Gets account_holders that has the name and password that the user entered when logging in / created the account
$holder_statement = $conn->prepare("
    SELECT * 
    FROM account_holders
    WHERE name = ? 
    AND password = ?
");

$holder_statement->bind_param("ss", $holder_name, $password);
$holder_statement->execute();
$holder_rows = $holder_statement->get_result();

$holder_row = $holder_rows->fetch_assoc();

try {
    // Checks if account_holder exists with inputted name and password
    if (!$holder_row) {
        header("Location: login.html");
        exit;
    }
    $holder_id = $holder_row['id'];

    // Gets all accounts of account holder
    $account_statement = $conn->prepare("SELECT * FROM accounts WHERE holder_id = ?");
    $account_statement->bind_param("i", $holder_id);
    $account_statement->execute();
    $account_rows = $account_statement->get_result();

    // Shows all accounts by replacing html template in a loop
    if ($account_rows && $account_rows->num_rows > 0) {
        while ($account_row = $account_rows->fetch_assoc()) {
            $id = $account_row["id"];
            $name = $account_row["holder_name"];
            $account_balance = $account_row["balance"];
            $tmp_string = $html_pieces[1];
            $tmp_string = str_replace(["---id---", "---name---", "---balance---"],
                [$id, $name, $account_balance], $tmp_string);
            echo $tmp_string;
        }
    } else {
        echo $html_pieces[1];
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

echo $html_pieces[2];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Creates new account for account holder
    if (isset($_POST["open_bank_account"])) {
        try {
            $balance = 0;
            // Checks if user made initial deposit
            if (isset($_POST["deposit"])) {
                $balance = strip_tags($_POST["deposit"]);
            }

            // Inserts the new account to the database and reloads the page
            $open_bank_statement = $conn->prepare("INSERT INTO accounts (holder_name, holder_id, balance) VALUES (?, ?, ?)");
            $open_bank_statement->bind_param("sii", $holder_name, $holder_id, $balance);
            $open_bank_statement->execute();
            $open_bank_statement->close();
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
