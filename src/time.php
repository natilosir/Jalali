<?php

namespace Natilosir\Jalali;

class time
{
    private static $jalaliMonths = [
        'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند',
    ];

    private static $jalaliDays = [
        'یک‌شنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه', 'شنبه',
    ];

    private static $suffixes = [
        '1'  => 'یکم', '2' => 'دوم', '3' => 'سوم', '4' => 'چهارم', '5' => 'پنجم',
        '6'  => 'ششم', '7' => 'هفتم', '8' => 'هشتم', '9' => 'نهم', '10' => 'دهم',
        '11' => 'یازدهم', '12' => 'دوازدهم', '13' => 'سیزدهم', '14' => 'چهاردهم',
        '15' => 'پانزدهم', '16' => 'شانزدهم', '17' => 'هفدهم', '18' => 'هجدهم',
        '19' => 'نوزدهم', '20' => 'بیستم', '21' => 'بیست‌و‌یکم', '22' => 'بیست‌و‌دوم',
        '23' => 'بیست‌و‌سوم', '24' => 'بیست‌و‌چهارم', '25' => 'بیست‌و‌پنجم',
        '26' => 'بیست‌و‌ششم', '27' => 'بیست‌و‌هفتم', '28' => 'بیست‌و‌هشتم',
        '29' => 'بیست‌و‌نهم', '30' => 'سی‌ام', '31' => 'سی‌و‌یکم',
    ];

    private static $timezoneOffset = 0;

    // تابعی برای تنظیم منطقه زمانی
    public static function Timezone($offset)
    {
        self::$timezoneOffset = $offset * 3600; // تبدیل ساعت به ثانیه
    }

    // تابعی برای تبدیل تایم‌استمپ به تاریخ جلالی
    public static function toj($timestamp, $format = null)
    {
        // افزودن جابجایی منطقه زمانی
        $timestamp += self::$timezoneOffset;

        list($gYear, $gMonth, $gDay) = explode('-', date('Y-m-d', $timestamp));
        list($jYear, $jMonth, $jDay) = self::ToJalali($gYear, $gMonth, $gDay);

        $time                    = date('H:i:s', $timestamp);

        $jalaliDateTime = sprintf('%04d/%02d/%02d %s', $jYear, $jMonth, $jDay, $time);

        if ($format) {
            return self::format($timestamp, $format, 'yes');
        }

        return new self($jalaliDateTime);
    }

    // تابعی برای تبدیل تاریخ جلالی به تایم‌استمپ
    public static function tot($jalaliDate, $hours = null)
    {
        list($datePart, $timePar)   = explode(' ', $jalaliDate) + [1 => '00:00:00'];
        list($jYear, $jMonth, $jDay) = explode('/', $datePart);
        list($gYear, $gMonth, $gDay) = self::ToGregorian($jYear, $jMonth, $jDay);
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
        list($datePart, $timePart)   = explode(' ', $this->jalaliDateTime);
        list($jYear, $jMonth, $jDay) = explode('/', $datePart);
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
        list($datePart, $timePart)   = explode(' ', $this->jalaliDateTime);
        list($jYear, $jMonth, $jDay) = explode('/', $datePart);
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

    public static function format($timestamp, $format = 'Y/m/d H:i:s', $TimeZone = null)
    {
        if (is_float($TimeZone)) {
            $timestamp += 3600 * $TimeZone;
        } elseif (is_null($TimeZone)) {
            $timestamp += self::$timezoneOffset;
        }
        list($gYear, $gMonth, $gDay) = explode('-', date('Y-m-d', $timestamp));
        list($jYear, $jMonth, $jDay) = self::ToJalali($gYear, $gMonth, $gDay);

        return strtr($format, [
            'Y' => $jYear,
            'y' => substr($jYear, -2),
            'm' => $jMonth,
            'M' => self::$jalaliMonths[$jMonth - 1],
            'd' => $jDay,
            'D' => self::$suffixes[$jDay],
            'W' => self::$jalaliDays[date('w', $timestamp)],
            'h' => date('h', $timestamp),
            'H' => date('H', $timestamp),
            'i' => date('i', $timestamp),
            's' => date('s', $timestamp),
        ]);
    }

    public static function miladi($jalaliDateTime)
    {
        $timestamp = self::tot($jalaliDateTime);

        return date('Y-m-d H:i:s', $timestamp);
    }
}
