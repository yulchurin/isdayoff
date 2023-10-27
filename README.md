### isdayoff.ru API Laravel implementation

[API Documentation](https://www.isdayoff.ru/extapi/)

### Usage

`composer require mactape/isdayoff`

```
IsDayOff::check()
# returns true || false
# by default will check the current day

$day = new DateTime('2023-12-12')
OR
$day = \Carbon\Carbon::parse('2023-12-12') 

IsDayOff::check($day)
```
