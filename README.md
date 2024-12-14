# Jalali Date Converter

This library helps you convert dates between the Gregorian (Miladi) and Jalali (Persian) calendars in PHP. Below are some examples of how to use the library.

### install
```bash
composer require natilosir/Jalali
```
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

### Code Example

Set the timezone to Tehran
```php
time::Timezone(3.5);
```
Convert timestamp to Jalali date
```php
$timestamp  = time();
$jalaliDate = time::toj($timestamp);
echo "Jalali Date: {$jalaliDate}\n";
```
Modify the Jalali date
```php
$modifiedDate = time::toj($timestamp)->addH(2)->addD(3)->addM(4)->addY(5);
echo "Modified Jalali Date: {$modifiedDate}\n";
```
Format the date in Persian
```php
echo time::format($timestamp, 'l d F Y H:i'); // Display the day, month, and year in Persian
```
Convert Jalali date to timestamp
```php
$jalaliDateInput    = '1402/09/24 14:30:00';
$convertedTimestamp = time::tot($jalaliDateInput);
echo "Timestamp: $convertedTimestamp\n";
```
Convert Jalali date to Gregorian using the miladi method
```php
echo time::miladi('1401/05/24 14:12:32'); // Display the equivalent Gregorian date
```
