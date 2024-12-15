# Jalali Date Converter

This library helps you convert dates between the Gregorian (Miladi) and Jalali (Persian) calendars in PHP. Below are some examples of how to use the library.

### Install
```bash
composer require natilosir/Jalali
```
Alternatively, you can clone the repository directly:
```bash
git clone https://github.com/natilosir/Jalali
```
### Example Usage

1. **Set the Timezone to Tehran**  
   You can set the timezone to Tehran (UTC+3.5) using the `Timezone` method.

2. **Convert Timestamp to Jalali Date**  
   Convert a Unix timestamp to a Jalali date using the `toj` method.

3. **Modify the Jalali Date**  
   You can modify a Jalali date by adding hours, days, months, or years using the `addH`, `addD`, `addM`, and `addY` methods.

4. **Format the Date in Persian**  
   Use the `format` method to display the date in Persian format.

5. **Convert Jalali Date to Timestamp**  
   You can convert a Jalali date string to a Unix timestamp using the `tot` method.

6. **Convert Jalali Date to Gregorian**  
   Use the `miladi` method to convert a Jalali date to its Gregorian counterpart.

---

## Supported Placeholders

| Placeholder | Description                                   | Example         |
|-------------|-----------------------------------------------|-----------------|
| `Y`         | Full Jalali year                             | `1403`          |
| `y`         | Last two digits of the Jalali year            | `03`            |
| `m`         | Numeric representation of the Jalali month    | `09`            |
| `M`         | Name of the Jalali month (in Persian)         | `آذر`           |
| `d`         | Numeric representation of the Jalali day      | `25`            |
| `D`         | Day suffix (in Persian)                      | `بیست‌و‌پنجم`   |
| `W`         | Day of the week (in Persian)                 | `یک‌شنبه`       |
| `h`         | Hour in 12-hour format                       | `06`            |
| `H`         | Hour in 24-hour format                       | `18`            |
| `i`         | Minutes                                       | `33`            |
| `s`         | Seconds                                       | `36`            |

---

## Example Usage

```php
// Set timezone offset (e.g., Iran Standard Time)
time::Timezone(3.5);

// Define a Gregorian timestamp
$timestamp = time();

// Format the Jalali date
$formattedDate = time::format($timestamp, 'W D M Y h:i:s');

// Output: یک‌شنبه بیست‌و‌پنجم آذر 1403 06:33:36
echo $formattedDate;
```
---
## Placeholders in Action

Given:
- Jalali date: `1403/09/25`
- Gregorian timestamp: `2024-12-15 06:33:36`

Using the format string `'W D M Y h:i:s'`, the replacements are:

```php
$timestamp = strtotime('2024-12-15 06:33:36');
$formattedDate = time::format($timestamp, 'W D M Y h:i:s');

// Output: یک‌شنبه بیست‌و‌پنجم آذر 1403 06:33:36
echo $formattedDate;
```
---

### Code Example

Set the timezone to Tehran
```php
time::Timezone(3.5); // tehran
```
Convert timestamp to Jalali date
```php
$timestamp  = time(); 
$jalaliDate = time::toj($timestamp);
echo "Jalali Date: {$jalaliDate}\n"; // Jalali Date: 1403/09/24 23:24:01
```
Modify the Jalali date
```php
$modifiedDate = time::toj($timestamp)->addH(2)->addD(3)->addM(4)->addY(5);
echo "Modified Jalali Date: {$modifiedDate}\n"; // Modified Jalali Date: 1409/01/28 01:24:01
```
Format the date in Persian
```php
echo time::format($timestamp, 'W D M Y h:i:s'); // یک‌شنبه بیست‌و‌پنجم آذر 1403 02:23:22
```
Convert Jalali date to timestamp
```php
$jalaliDateInput    = '1402/09/24 14:30:00';
$convertedTimestamp = time::tot($jalaliDateInput);
echo "Timestamp: $convertedTimestamp\n"; // Timestamp: 1702638000
```
Convert Jalali date to Gregorian using the miladi method
```php
echo time::miladi('1401/05/24 14:12:32'); // 2022-08-15 10:42:32
```

