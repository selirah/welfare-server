<?php

namespace App\Helpers;


class Helper
{
    const apiKey = "daa617a455dba72d84be";
    const endPoint = "http://sms.ebitsgh.com/smsapi";

    public static function sendSMS($phone, $message, $sender = 'EBITS GH')
    {
        $send = self::endPoint . '?key=' . self::apiKey . '&to=' . $phone . '&msg=' . $message . '&sender_id=' . $sender;
        return file_get_contents($send);
    }

    public function sendBulkSMS($senderId, array $student)
    {
        foreach ($student as $s) {
            $message = "Hello " . $s['other_names'] . " " . $s['surname'] . ", you have been offered admission to pursue a " . $s['duration'] . "-year programme for the " . $s['academic_year'] . " academic year. Visit  http://admission.ebitsapps.com to print letter. App No : " . $s['application_number'] . "  PIN : " . $s['pin'] . ". Please visit https://admissionsghana.com to learn more";
            $this->_sendSMS($s['phone'], $message, $senderId);
        }
    }

    private function _sendSMS($phone, $message, $senderId)
    {
        $message = urlencode($message);
        $send = self::endPoint . '?key=' . self::apiKey . '&to=' . $phone . '&msg=' . $message . '&sender_id=' . $senderId;
        file_get_contents($send);
    }

    public static function generateCode($len = 5)
    {
        $code = substr(md5(time()), 10, $len);
        return $code;
    }

    public static function generateRandomPassword()
    {
        try {
            $bytes = random_bytes(3);
            $randomPassword = strtoupper(bin2hex($bytes));
            return $randomPassword;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public static function sanitizePhone($phone)
    {
        $phone = str_replace(" ", "", $phone);
        $phone = str_replace("-", "", $phone);
        $phone = str_replace("+", "", $phone);
        filter_var($phone, FILTER_SANITIZE_NUMBER_INT);

        if ((substr($phone, 0, 1) == 0) && (strlen($phone) == 10)) {
            return substr_replace($phone, "233", 0, 1);
        } elseif ((substr($phone, 0, 1) != 0) && (strlen($phone) == 9)) {
            return "233" . $phone;
        } elseif ((substr($phone, 0, 3) == "233") && (strlen($phone) == 12)) {
            return $phone;
        } elseif ((substr($phone, 0, 5) == "00233") && (strlen($phone) == 14)) { //if number begin with 233 and length is 12
            return substr_replace($phone, "233", 0, 5);
        } else {
            return $phone;
        }
    }

    public static function generateGravatar($email, $s = 200, $r = 'pg', $d = 'mm')
    {
        $email = md5(strtolower(trim($email)));
        $gravatarUrl = "http://www.gravatar.com/avatar/" . $email . "?d=" . $d . "&s=" . $s . "&r=" . $r;
        return $gravatarUrl;
    }

    public static function getMonth($m)
    {
        $MONTHS = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        return $MONTHS[$m - 1];
    }
}
