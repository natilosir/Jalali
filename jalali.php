<?php

class time
{
    private static $jalaliMonths = [
        'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند',
    ];

    private static $jalaliDays = [
        'یک‌شنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه', 'شنبه',
    ];

    private static $timezoneOffset = 0; // جابجایی منطقه زمانی بر حسب ساعت

    // تابعی برای تنظیم منطقه زمانی
    public static function Timezone($offset)
    {
        self::$timezoneOffset = $offset * 3600; // تبدیل ساعت به ثانیه
    }

    // تابعی برای تبدیل تایم‌استمپ به تاریخ جلالی
    public static function toj($timestamp)
    {
        $timestamp += self::$timezoneOffset; // اعمال جابجایی منطقه زمانی
        [$gYear, $gMonth, $gDay] = explode('-', date('Y-m-d', $timestamp));
        [$jYear, $jMonth, $jDay] = self::ToJalali($gYear, $gMonth, $gDay);
        $time                    = date('H:i:s', $timestamp);

        return new self(sprintf('%04d/%02d/%02d %s', $jYear, $jMonth, $jDay, $time));
    }

    // تابعی برای تبدیل تاریخ جلالی به تایم‌استمپ

    public static function tot($jalaliDate, $hours = null)
    {
        [$datePart, $timePart]   = explode(' ', $jalaliDate) + [1 => '00:00:00'];
        [$jYear, $jMonth, $jDay] = explode('/', $datePart);
        [$gYear, $gMonth, $gDay] = self::ToGregorian($jYear, $jMonth, $jDay);
        $gregorianDate           = sprintf('%04d-%02d-%02d %s', $gYear, $gMonth, $gDay, $timePart);
        $timestamp               = strtotime($gregorianDate);

        return ($timestamp - self::$timezoneOffset) + ($hours * 3600); // اعمال جابجایی معکوس منطقه زمانی
    }

    private $jalaliDateTime;

    public function __construct($jalaliDateTime)
    {
        $this->jalaliDateTime = $jalaliDateTime;
    }

    public function addH($hours)
    {
        $timestamp = self::tot($this->jalaliDateTime);
        $timestamp += $hours * 3600;
        $this->jalaliDateTime = self::toj($timestamp)->jalaliDateTime;

        return $this;
    }

    public function addD($days)
    {
        $timestamp = self::tot($this->jalaliDateTime);
        $timestamp += $days * 86400;
        $this->jalaliDateTime = self::toj($timestamp)->jalaliDateTime;

        return $this;
    }

    public function addM($months)
    {
        [$datePart, $timePart]   = explode(' ', $this->jalaliDateTime);
        [$jYear, $jMonth, $jDay] = explode('/', $datePart);
        $jMonth += $months;

        while ($jMonth > 12) {
            $jMonth -= 12;
            $jYear++;
        }
        while ($jMonth < 1) {
            $jMonth += 12;
            $jYear--;
        }

        $this->jalaliDateTime = sprintf('%04d/%02d/%02d %s', $jYear, $jMonth, $jDay, $timePart);

        return $this;
    }

    public function addY($years)
    {
        [$datePart, $timePart]   = explode(' ', $this->jalaliDateTime);
        [$jYear, $jMonth, $jDay] = explode('/', $datePart);
        $jYear += $years;

        $this->jalaliDateTime = sprintf('%04d/%02d/%02d %s', $jYear, $jMonth, $jDay, $timePart);

        return $this;
    }

    // تبدیل تاریخ جلالی به رشته بدون نیاز به متد get
    public function __toString()
    {
        return $this->jalaliDateTime;
    }

    // تابع تبدیل میلادی به جلالی
    private static function ToJalali($gYear, $gMonth, $gDay)
    {
        $gDaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $jDaysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy = $gYear - 1600;
        $gm = $gMonth - 1;
        $gd = $gDay - 1;

        $gDayNo = 365 * $gy + (int) (($gy + 3) / 4) - (int) (($gy + 99) / 100) + (int) (($gy + 399) / 400);

        for ($i = 0; $i < $gm; $i++) {
            $gDayNo += $gDaysInMonth[$i];
        }

        if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) {
            $gDayNo++;
        }

        $gDayNo += $gd;

        $jDayNo = $gDayNo - 79;

        $jNp = (int) ($jDayNo / 12053);
        $jDayNo %= 12053;

        $jYear = 979 + 33 * $jNp + 4 * (int) ($jDayNo / 1461);

        $jDayNo %= 1461;

        if ($jDayNo >= 366) {
            $jYear += (int) (($jDayNo - 1) / 365);
            $jDayNo = ($jDayNo - 1) % 365;
        }

        for ($i = 0; $i < 11 && $jDayNo >= $jDaysInMonth[$i]; $i++) {
            $jDayNo -= $jDaysInMonth[$i];
        }

        $jMonth = $i + 1;
        $jDay   = $jDayNo + 1;

        return [$jYear, $jMonth, $jDay];
    }

    // تابع تبدیل جلالی به میلادی
    private static function ToGregorian($jYear, $jMonth, $jDay)
    {
        $gDaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $jDaysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $jYear -= 979;
        $jMonth--;
        $jDay--;

        $jDayNo = 365 * $jYear + (int) ($jYear / 33) * 8 + (int) (($jYear % 33 + 3) / 4);

        for ($i = 0; $i < $jMonth; $i++) {
            $jDayNo += $jDaysInMonth[$i];
        }

        $jDayNo += $jDay;

        $gDayNo = $jDayNo + 79;

        $gy = 1600 + 400 * (int) ($gDayNo / 146097);
        $gDayNo %= 146097;

        $leap = true;

        if ($gDayNo >= 36525) {
            $gDayNo--;
            $gy += 100 * (int) ($gDayNo / 36524);
            $gDayNo %= 36524;

            if ($gDayNo >= 365) {
                $gDayNo++;
            } else {
                $leap = false;
            }
        }

        $gy += 4 * (int) ($gDayNo / 1461);
        $gDayNo %= 1461;

        if ($gDayNo >= 366) {
            $leap = false;
            $gDayNo--;
            $gy += (int) ($gDayNo / 365);
            $gDayNo %= 365;
        }

        for ($i = 0; $gDayNo >= $gDaysInMonth[$i] + ($i == 1 && $leap); $i++) {
            $gDayNo -= $gDaysInMonth[$i] + ($i == 1 && $leap);
        }

        $gMonth = $i + 1;
        $gDay   = $gDayNo + 1;

        return [$gy, $gMonth, $gDay];
    }

    // تابعی برای فرمت‌دهی با متن فارسی
    public static function format($timestamp, $format = 'Y/m/d H:i:s')
    {
        $timestamp += self::$timezoneOffset; // اعمال جابجایی منطقه زمانی
        [$gYear, $gMonth, $gDay] = explode('-', date('Y-m-d', $timestamp));
        [$jYear, $jMonth, $jDay] = self::ToJalali($gYear, $gMonth, $gDay);

        $formattedDate = $format;
        $formattedDate = str_replace('Y', $jYear, $formattedDate);
        $formattedDate = str_replace('y', substr($jYear, -2), $formattedDate);
        $formattedDate = str_replace('m', sprintf('%02d', $jMonth), $formattedDate);
        $formattedDate = str_replace('d', sprintf('%02d', $jDay), $formattedDate);
        $formattedDate = str_replace('F', self::$jalaliMonths[$jMonth - 1], $formattedDate);
        $formattedDate = str_replace('l', self::$jalaliDays[date('w', $timestamp)], $formattedDate);
        $formattedDate = str_replace('h', sprintf('%02d', date('h', $timestamp)), $formattedDate);
        $formattedDate = str_replace('i', sprintf('%02d', date('i', $timestamp)), $formattedDate);
        $formattedDate = str_replace('s', sprintf('%02d', date('s', $timestamp)), $formattedDate);

        return $formattedDate;
    }

    // تبدیل جلالی به میلادی با فرمت به‌صورت متد نمونه
    public static function miladi($jalaliDateTime)
    {
        $timestamp = self::tot($jalaliDateTime);

        return date('Y-m-d H:i:s', $timestamp);
    }
}
// Example usage
// Set the timezone to Tehran
time::Timezone(3.5);

// Convert timestamp to Jalali date
$timestamp  = time();
$jalaliDate = time::toj($timestamp);
echo "Jalali Date: {$jalaliDate}\n";

// Modify the Jalali date
$modifiedDate = time::toj($timestamp)->addH(2)->addD(3)->addM(4)->addY(5);
echo "Modified Jalali Date: {$modifiedDate}\n";

// Format the date in Persian
echo time::format($timestamp, 'l d F Y h:i:s'); // Display the day, month, and year in Persian

// Convert Jalali date to timestamp
$jalaliDateInput    = '1402/09/24';
$convertedTimestamp = time::tot($jalaliDateInput);
echo "\nTimestamp: $convertedTimestamp\n";

// Convert Jalali date to Gregorian using the miladi method
echo time::miladi('1401/05/24 14:12:32'); // Display the equivalent Gregorian date
