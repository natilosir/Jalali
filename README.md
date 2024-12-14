# Jalali Date Converter

This tool helps in converting dates between the Gregorian and Jalali calendars. Here is an example of how to use the tool and some of its features.

### Example Usage

1. **Setting the Timezone to Tehran**  
   Set the timezone to Tehran (UTC+3.5).

2. **Convert Timestamp to Jalali Date**  
   Convert a Unix timestamp to a Jalali date.

3. **Modify the Jalali Date**  
   Add hours, days, months, or years to the Jalali date.

4. **Format the Date in Persian**  
   Format the Jalali date in a readable format in Persian.

5. **Convert Jalali Date to Timestamp**  
   Convert a Jalali date string to a Unix timestamp.

6. **Convert Jalali Date to Gregorian Date**  
   Convert a Jalali date to the Gregorian calendar.

### Code Example

```php
// Example usage
// Set the timezone to Tehran
time::Timezone(3.5);

// Convert timestamp to Jalali date
$timestamp = time();
$jalaliDate = time::toj($timestamp);
echo "Jalali Date: {$jalaliDate}\n";

// Modify the Jalali date
$modifiedDate = time::toj($timestamp)->addH(2)->addD(3)->addM(4)->addY(5);
echo "Modified Jalali Date: {$modifiedDate}\n";

// Format the date in Persian
echo time::format($timestamp, 'l d F Y H:i'); // Display the day, month, and year in Persian

// Convert Jalali date to timestamp
$jalaliDateInput = '1402/09/24 14:30:00';
$convertedTimestamp = time::tot($jalaliDateInput);
echo "Timestamp: $convertedTimestamp\n";

// Convert Jalali date to Gregorian using the miladi method
echo time::miladi('1401/05/24 14:12:32'); // Display the equivalent Gregorian date
