<?php

function _contains($haystack, $needle, $offset = 0)
{
    if (empty($haystack)) {
        return false;
    }
    $pos = strpos($haystack, $needle, $offset);
    return $pos !== false;
}

function _health_map($ping, $pong)
{
    $is = abs(time() - strtotime($ping));
    $os = abs(time() - strtotime($pong));

    $data = (object)[
        'ping' => (object)[
            's' => $is,
            'd' => _days_ago($ping)
        ],
        'pong' => (object)[
            's' => $os,
            'd' => _days_ago($pong)
        ]
    ];

    return $data;
}

function _timestamp()
{
    return date("Y-m-d H:i:s");
}

function _days_count($t)
{
    $d = abs(time() - strtotime($t));
    $c = round($d / 86400, 2);
    return $c;
}

function _days_ago($target)
{
    $lang_date_days = [
        ['name' => '개월', 'amount' => 60 * 60 * 24 * 30],
        ['name' => '일', 'amount' => 60 * 60 * 24],
        ['name' => '시간', 'amount' => 60 * 60],
        ['name' => '분', 'amount' => 60],
        ['name' => '초', 'amount' => 1]
    ];
    $diff = abs(time() - strtotime($target));
    $result = '';
    foreach ($lang_date_days as $block) {
        if ($diff / $block['amount'] >= 1) {
            $amount = round($diff / $block['amount']);
            $result = $amount . $block['name'];
            break;
        }
    }
    if (empty($result)) {
        return '방금';
    } else {
        return $result; // . ' 전';
    }
}

function _phone_format($num)
{
    $num = preg_replace('/[^0-9]/', '', $num);
    $len = strlen($num);
    if ($len == 10) {
        $num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '$1-$2-$3', $num);
    } else if ($len == 11) {
        $num = preg_replace('/([0-9]{3})([0-9]{4})([0-9]{4})/', '$1-$2-$3', $num);
    }
    return $num;
}

function _phone_mask($num)
{
    $num = preg_replace('/[^0-9]/', '', $num);
    $len = strlen($num);
    if ($len == 10) {
        $num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '$1-***-$3', $num);
    } else if ($len == 11) {
        $num = preg_replace('/([0-9]{3})([0-9]{4})([0-9]{4})/', '$1-****-$3', $num);
    }
    return $num;
}
