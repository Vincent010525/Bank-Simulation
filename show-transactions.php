<?php
// Shows accounts transactions, and allows accounts to make new transactions
header("Content-Type: text/html; charset=utf-8");

$html = file_get_contents("transactions.html");

$host = "localhost";
$user = "root";
$pass = "";
$db = "bank";

session_start();

// Saves account id to session when the user gets to the page for the first time
// since that's the only time the if statement goes through
if (isset($_GET["account_id"])) {
    $account_id = $_GET["account_id"];
    $_SESSION["account_id"] = $account_id;
}

$from_account = "";
$to_account = "";
$description = "";

// Establishing database connection
$conn = mysqli_connect($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handles user making a new transaction
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account_id = $_SESSION["account_id"];
    $transaction_from_account = null;
    $transaction_to_account = null;
    $transaction_time = date("Y-m-d H:i:s");
    $transaction_amount = $_POST["amount"];
    $transaction_type = $_POST["transaction_type"];
    $description = null;
    if (isset($_POST["description"])) {
        $description = $_POST["description"];
    }
    if ($transaction_type == "deposit") {
        $transaction_to_account = $account_id;
        // Adds the deposit amount to account
        $statement = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
        $statement->bind_param("ii", $transaction_amount, $account_id);
        $statement->execute();
        $statement->close();
    } else {
        // Code that runs if user is withdrawing or transfering money to another account
        $transaction_from_account = $account_id;
        $old_balance = 0;
        // Gets the account balance
        $balance_statement = $conn->prepare("SELECT balance FROM accounts WHERE id = ?");
        $balance_statement->bind_param("i", $account_id);
        $balance_statement->execute();
        $balance_statement->bind_result($old_balance);
        $balance_statement->fetch();
        $balance_statement->close();
        // Reloads the page and exits transaction if the account balance is too low
        if ($transaction_amount > $old_balance) {
            header("Location: show-transactions.php?account_id=$account_id");
            exit;
        }
        if ($transaction_type == "withdraw") {
            // Subtracts the withdrawal amount from the account balance
            $statement = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
            $statement->bind_param("ii", $transaction_amount, $account_id);
            $statement->execute();
            $statement->close();
        } else if ($transaction_type == "transfer") {
            $receiver_balance = 0;
            // Cancels the transfer and reloads the page if there is no receiver or
            // if the receiver is the account that is making the transfer
            if (empty($_POST["receiver"]) || $_POST["receiver"] == $account_id) {
                header("Location: show-transactions.php?account_id=$account_id");
                exit;
            }
            $receiver_id = $_POST["receiver"];
            $transaction_to_account = $receiver_id;
            // Gets the receiver account
            $receiver_statement = $conn->prepare("SELECT * FROM accounts WHERE id = ?");
            $receiver_statement->bind_param("i", $receiver_id);
            $receiver_statement->execute();
            $rows = $receiver_statement->get_result();
            $receiver_statement->close();
            // Cancels the transfer and reloads the page if receiver doesnt exist
            if ($rows->num_rows == 0) {
                header("Location: show-transactions.php?account_id=$account_id");
                exit;
            }

            // Adds the money transfered to the receiver account
            $update_receiver_statement = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
            $update_receiver_statement->bind_param("ii", $transaction_amount, $receiver_id);
            $update_receiver_statement->execute();
            $update_receiver_statement->close();

            // Subtracts the money sent from the account making the transfer
            $update_account_statement = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
            $update_account_statement->bind_param("ii", $transaction_amount, $account_id);
            $update_account_statement->execute();
            $update_account_statement->close();
        }
    }

    // Inserts the new transfer to the database and reloads the page
    $make_transaction_statement = $conn->prepare("
        INSERT INTO transactions (type, from_account_id, to_account_id, amount, time_stamp, description)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $make_transaction_statement->bind_param("siidss", $transaction_type, $transaction_from_account, $transaction_to_account, $transaction_amount, $transaction_time, $description);
    $make_transaction_statement->execute();
    $make_transaction_statement->close();
    header("Location: show-transactions.php?account_id=$account_id");
    exit;
}

$account_id = $_SESSION["account_id"];
$account_balance = 0;

// Gets account balance
$account_balance_statement = $conn->prepare("SELECT balance FROM accounts WHERE id = ?");
$account_balance_statement->bind_param("i", $account_id);
$account_balance_statement->execute();
$account_balance_statement->bind_result($account_balance);
$account_balance_statement->fetch();
$account_balance_statement->close();

// Replaces balance template with actual account balance
$html = str_replace("---balance---", $account_balance, $html);

$html_pieces = explode("<!--===entries===-->", $html);

echo $html_pieces[0];

// Gets all transactions either sent or received
$transactions_statement = $conn->prepare("
    SELECT * 
    FROM transactions 
    WHERE from_account_id = ? 
       OR to_account_id = ?
");
$transactions_statement->bind_param("ii", $account_id, $account_id);
$transactions_statement->execute();
$transactions_rows = $transactions_statement->get_result();

// Shows all transfers by replacing html template in a loop
if ($transactions_rows && $transactions_rows->num_rows > 0) {
    while ($transactions_row = $transactions_rows->fetch_assoc()) {
        $id = $transactions_row["id"];
        $type = $transactions_row["type"];
        if (!is_null($transactions_row["from_account_id"])) {
            $from_account = $transactions_row["from_account_id"];
        } else {
            $from_account = "";
        }
        if (!is_null($transactions_row["to_account_id"])) {
            $to_account = $transactions_row["to_account_id"];
        } else {
            $to_account = "";
        }
        if (!is_null($transactions_row["description"])) {
            $description = $transactions_row["description"];
        }
        $amount = $transactions_row["amount"];
        $time = $transactions_row["time_stamp"];
        $tmp_string = $html_pieces[1];
        $tmp_string = str_replace(["---id---", "---time---", "---type---", "---from---", "---to---", "---amount---", "---comment---"],
            [$id, $time, $type, $from_account, $to_account, $amount, $description], $tmp_string);
        echo $tmp_string;
    }
} else {
    echo $html_pieces[1];
}

echo $html_pieces[2];