<?php

include_once('db.php');

$conn = get_connect();

/**
 * Return list of users.
 */
function get_users($conn)
{
    $users = $conn->query("SELECT * FROM users");
    return $users->fetchAll(PDO::FETCH_ASSOC);
}

function get_user_accounts($conn, $user_id)
{
    $accounts = $conn->query("SELECT * FROM user_accounts WHERE user_id = $user_id");
    return $accounts->fetchAll(PDO::FETCH_ASSOC);
}

function get_user_monthly_balances($user_id, $conn)
{
    $monthly_balances = [];

    // Получаем аккаунты пользователя
    $accounts = $conn->query("SELECT * FROM user_accounts WHERE user_id = $user_id")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($accounts as $account) {
        // Получаем все транзакции для аккаунта
        $transactions = $conn->query("SELECT * FROM transactions WHERE account_from = {$account['id']} OR account_to = {$account['id']}")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($transactions as $transaction) {
            // Получаем месяц и год из даты транзакции
            $date = new DateTime($transaction['trdate']);
            $month = $date->format('m');
            $year = $date->format('Y');

            // Инициализируем массив для месяца, если его еще нет
            if (!isset($monthly_balances["$year-$month"])) {
                $monthly_balances["$year-$month"] = 0;
            }

            // Если транзакция входящая
            if ($transaction['account_to'] == $account['id']) {
                $monthly_balances["$year-$month"] += $transaction['amount'];
            }
            // Если транзакция исходящая
            elseif ($transaction['account_from'] == $account['id']) {
                $monthly_balances["$year-$month"] -= $transaction['amount'];
            }
        }
    }

    return $monthly_balances;
}