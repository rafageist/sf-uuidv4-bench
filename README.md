# Symfony UuidV4 Optimization & Performances

This change enhances the generation of the 19th character in UUIDv4 in the Symfony UuidV4 class. The Process ID (PID) has been implemented as a sufficiently random and efficient source.

https://github.com/symfony/symfony/pull/54027

## Implementation Details

- A Singleton has been introduced to store the PID and avoid its repeated retrieval during script execution.
- The character at position 19 is now set using the remainder of the PID divided by 4, ensuring a uniform distribution among ['8','9','a','b'].

## Reasons for the Improvement

- The generation of the 19th character is done more efficiently by using the PID as a random source.
- The use of the Singleton ensures the PID is obtained only once during script execution.

## Tested Variants

- Variants 1 to 6 have been tested, and the PID-based Variant 7 has shown improvements in efficiency.

# Variants

| Benchmark                | Description                                                                       | Implementation                                                            |
|--------------------------|-----------------------------------------------------------------------------------|---------------------------------------------------------------------------|
| `benchUuidV4Current()`   | Replaces the 19th character with a mapping based on its current value.           | A mapping array is used to determine the replacement for the 19th character. |
| `benchUuidV4Variant1()`  | Uses the least significant 2 bits of the first character to select the replacement for the 19th character. | The first character is used to determine the replacement based on its 2 least significant bits. |
| `benchUuidV4Variant3()`  | Utilizes mathematical operations on the ASCII value of the 19th character for replacement. | The ASCII value is modified and transformed into a character based on a specific formula. |
| `benchUuidV4Variant4()`  | Applies bitwise and mathematical operations to the ASCII value of the 19th character for replacement. | Bitwise operations and mathematical transformations are performed on the ASCII value to determine the replacement. |
| `benchUuidV4Variant5()`  | Randomly selects a replacement for the 19th character.                             | A random replacement is chosen from the available options.                |
| `benchUuidV4Variant6()`  | Uses the current timestamp modulo 4 to determine the replacement for the 19th character. | The current timestamp's remainder after division by 4 is used to select the replacement. |
| `benchUuidV4Variant7()`  | Incorporates the process ID (PID) to determine the replacement for the 19th character. | The process ID (PID) is used to select the replacement from the available options. |


## General Notes

- All variants iterate over a set of 50 pre-defined UUIDs for testing purposes.
- The 19th character replacement is performed in a loop for each UUID in the set.
- Variants 1 to 6 are primarily based on character manipulation or random selection.
- Variant 7 introduces the use of the process ID for potentially improved randomness.
- These benchmarks aim to evaluate the performance of different strategies for generating UUIDv4 values with a focus on the 19th character.

## Code

### Before 

```php
<?php

$uuid = bin2hex(random_bytes(18));
$uuid[8] = $uuid[13] = $uuid[18] = $uuid[23] = '-';
$uuid[14] = '4';
$uuid[19] = ['8', '9', 'a', 'b', '8', '9', 'a', 'b', 'c' => '8', 'd' => '9', 'e' => 'a', 'f' => 'b'][$uuid[19]] ?? $uuid[19];
```

### After 

```php
$uuid = bin2hex(random_bytes(18));
$uuid[8] = $uuid[13] = $uuid[18] = $uuid[23] = '-';
$uuid[14] = '4';
$uuid[19] = ['8', '9', 'a', 'b'][getmypid() % 4];
```

Note: getmypid() can be cached

## Benchmarks

Performance tests have been conducted, and the results indicate improvements in efficiency with this implementation.

### Run

```shell
make install
make bench > results.txt
```

### Environment

* PHP 8.2.4 (cli) (built: Mar 14 2023 17:54:25) (ZTS Visual C++ 2019 x64) Zend Engine v4.2.4, Copyright (c) Zend Technologies
* opcache disabled
* xdebug disabled

### Results

```
php vendor/bin/phpbench run --report=aggregate
+------------+---------------------+-----+------+-----+-----------+----------+--------+
| benchmark  | subject             | set | revs | its | mem_peak  | mode     | rstdev |
+------------+---------------------+-----+------+-----+-----------+----------+--------+
| MultiBench | benchUuidV4Current  |     | 1    | 1   | 682.016kb | 10.000μs | ±0.00% |
| MultiBench | benchUuidV4Variant1 |     | 1    | 1   | 682.016kb | 16.000μs | ±0.00% |
| MultiBench | benchUuidV4Variant3 |     | 1    | 1   | 682.016kb | 22.000μs | ±0.00% |
| MultiBench | benchUuidV4Variant4 |     | 1    | 1   | 682.016kb | 23.000μs | ±0.00% |
| MultiBench | benchUuidV4Variant5 |     | 1    | 1   | 682.016kb | 29.000μs | ±0.00% |
| MultiBench | benchUuidV4Variant6 |     | 1    | 1   | 682.016kb | 18.000μs | ±0.00% |
| MultiBench | benchUuidV4Variant7 |     | 1    | 1   | 682.016kb | 19.000μs | ±0.00% |
| MonoBench  | benchUuidV4Current  |     | 1    | 1   | 682.016kb | 4.601ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant1 |     | 1    | 1   | 682.016kb | 4.717ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant3 |     | 1    | 1   | 682.016kb | 10.478ms | ±0.00% |
| MonoBench  | benchUuidV4Variant4 |     | 1    | 1   | 682.016kb | 9.844ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant5 |     | 1    | 1   | 682.016kb | 11.008ms | ±0.00% |
| MonoBench  | benchUuidV4Variant6 |     | 1    | 1   | 682.016kb | 6.734ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant7 |     | 1    | 1   | 682.016kb | 3.540ms  | ±0.00% |
+------------+---------------------+-----+------+-----+-----------+----------+--------+

php vendor/bin/phpbench run --report=aggregate --revs=100

+------------+---------------------+-----+------+-----+-----------+----------+--------+
| benchmark  | subject             | set | revs | its | mem_peak  | mode     | rstdev |
+------------+---------------------+-----+------+-----+-----------+----------+--------+
| MultiBench | benchUuidV4Current  |     | 100  | 1   | 682.016kb | 4.280μs  | ±0.00% |
| MultiBench | benchUuidV4Variant1 |     | 100  | 1   | 682.016kb | 4.830μs  | ±0.00% |
| MultiBench | benchUuidV4Variant3 |     | 100  | 1   | 682.016kb | 11.010μs | ±0.00% |
| MultiBench | benchUuidV4Variant4 |     | 100  | 1   | 682.016kb | 10.040μs | ±0.00% |
| MultiBench | benchUuidV4Variant5 |     | 100  | 1   | 682.016kb | 6.820μs  | ±0.00% |
| MultiBench | benchUuidV4Variant6 |     | 100  | 1   | 682.016kb | 5.380μs  | ±0.00% |
| MultiBench | benchUuidV4Variant7 |     | 100  | 1   | 682.016kb | 5.540μs  | ±0.00% |
| MonoBench  | benchUuidV4Current  |     | 100  | 1   | 682.016kb | 4.363ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant1 |     | 100  | 1   | 682.016kb | 4.898ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant3 |     | 100  | 1   | 682.016kb | 11.967ms | ±0.00% |
| MonoBench  | benchUuidV4Variant4 |     | 100  | 1   | 682.016kb | 10.408ms | ±0.00% |
| MonoBench  | benchUuidV4Variant5 |     | 100  | 1   | 682.016kb | 7.468ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant6 |     | 100  | 1   | 682.016kb | 5.966ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant7 |     | 100  | 1   | 682.016kb | 4.168ms  | ±0.00% |
+------------+---------------------+-----+------+-----+-----------+----------+--------+

php vendor/bin/phpbench run --report=aggregate --revs=10000

+------------+---------------------+-----+-------+-----+-----------+----------+--------+
| benchmark  | subject             | set | revs  | its | mem_peak  | mode     | rstdev |
+------------+---------------------+-----+-------+-----+-----------+----------+--------+
| MultiBench | benchUuidV4Current  |     | 10000 | 1   | 682.016kb | 5.486μs  | ±0.00% |
| MultiBench | benchUuidV4Variant1 |     | 10000 | 1   | 682.016kb | 5.640μs  | ±0.00% |
| MultiBench | benchUuidV4Variant3 |     | 10000 | 1   | 682.016kb | 11.507μs | ±0.00% |
| MultiBench | benchUuidV4Variant4 |     | 10000 | 1   | 682.016kb | 10.515μs | ±0.00% |
| MultiBench | benchUuidV4Variant5 |     | 10000 | 1   | 682.016kb | 7.143μs  | ±0.00% |
| MultiBench | benchUuidV4Variant6 |     | 10000 | 1   | 682.016kb | 6.130μs  | ±0.00% |
| MultiBench | benchUuidV4Variant7 |     | 10000 | 1   | 682.016kb | 5.966μs  | ±0.00% |
| MonoBench  | benchUuidV4Current  |     | 10000 | 1   | 682.016kb | 4.569ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant1 |     | 10000 | 1   | 682.016kb | 5.172ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant3 |     | 10000 | 1   | 682.016kb | 11.483ms | ±0.00% |
| MonoBench  | benchUuidV4Variant4 |     | 10000 | 1   | 682.016kb | 10.791ms | ±0.00% |
| MonoBench  | benchUuidV4Variant5 |     | 10000 | 1   | 682.016kb | 7.719ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant6 |     | 10000 | 1   | 682.016kb | 5.983ms  | ±0.00% |
| MonoBench  | benchUuidV4Variant7 |     | 10000 | 1   | 682.016kb | 3.977ms  | ±0.00% |
+------------+---------------------+-----+-------+-----+-----------+----------+--------+

php vendor/bin/phpbench run --report=aggregate --iterations=100

+------------+---------------------+-----+------+-----+-----------+----------+---------+
| benchmark  | subject             | set | revs | its | mem_peak  | mode     | rstdev  |
+------------+---------------------+-----+------+-----+-----------+----------+---------+
| MultiBench | benchUuidV4Current  |     | 1    | 100 | 682.016kb | 10.990μs | ±16.39% |
| MultiBench | benchUuidV4Variant1 |     | 1    | 100 | 682.016kb | 18.331μs | ±42.77% |
| MultiBench | benchUuidV4Variant3 |     | 1    | 100 | 682.016kb | 22.493μs | ±26.08% |
| MultiBench | benchUuidV4Variant4 |     | 1    | 100 | 682.016kb | 23.292μs | ±24.37% |
| MultiBench | benchUuidV4Variant5 |     | 1    | 100 | 682.016kb | 28.679μs | ±61.32% |
| MultiBench | benchUuidV4Variant6 |     | 1    | 100 | 682.016kb | 15.231μs | ±46.02% |
| MultiBench | benchUuidV4Variant7 |     | 1    | 100 | 682.016kb | 19.405μs | ±10.26% |
| MonoBench  | benchUuidV4Current  |     | 1    | 100 | 682.016kb | 4.294ms  | ±31.65% |
| MonoBench  | benchUuidV4Variant1 |     | 1    | 100 | 682.016kb | 4.808ms  | ±16.51% |
| MonoBench  | benchUuidV4Variant3 |     | 1    | 100 | 682.016kb | 11.072ms | ±13.37% |
| MonoBench  | benchUuidV4Variant4 |     | 1    | 100 | 682.016kb | 10.132ms | ±17.31% |
| MonoBench  | benchUuidV4Variant5 |     | 1    | 100 | 682.016kb | 7.181ms  | ±12.79% |
| MonoBench  | benchUuidV4Variant6 |     | 1    | 100 | 682.016kb | 5.569ms  | ±24.61% |
| MonoBench  | benchUuidV4Variant7 |     | 1    | 100 | 682.016kb | 3.705ms  | ±14.26% |
+------------+---------------------+-----+------+-----+-----------+----------+---------+

php vendor/bin/phpbench run --report=aggregate --revs=100 --iterations=100

+------------+---------------------+-----+------+-----+-----------+----------+----------+
| benchmark  | subject             | set | revs | its | mem_peak  | mode     | rstdev   |
+------------+---------------------+-----+------+-----+-----------+----------+----------+
| MultiBench | benchUuidV4Current  |     | 100  | 100 | 682.016kb | 4.632μs  | ±206.68% |
| MultiBench | benchUuidV4Variant1 |     | 100  | 100 | 682.016kb | 4.822μs  | ±27.77%  |
| MultiBench | benchUuidV4Variant3 |     | 100  | 100 | 682.016kb | 10.781μs | ±23.51%  |
| MultiBench | benchUuidV4Variant4 |     | 100  | 100 | 682.016kb | 10.108μs | ±19.24%  |
| MultiBench | benchUuidV4Variant5 |     | 100  | 100 | 682.016kb | 7.169μs  | ±22.95%  |
| MultiBench | benchUuidV4Variant6 |     | 100  | 100 | 682.016kb | 5.468μs  | ±18.77%  |
| MultiBench | benchUuidV4Variant7 |     | 100  | 100 | 682.016kb | 5.519μs  | ±15.03%  |
| MonoBench  | benchUuidV4Current  |     | 100  | 100 | 682.016kb | 4.596ms  | ±4.16%   |
| MonoBench  | benchUuidV4Variant1 |     | 100  | 100 | 682.016kb | 5.040ms  | ±2.84%   |
| MonoBench  | benchUuidV4Variant3 |     | 100  | 100 | 682.016kb | 11.342ms | ±3.81%   |
| MonoBench  | benchUuidV4Variant4 |     | 100  | 100 | 682.016kb | 10.763ms | ±5.04%   |
| MonoBench  | benchUuidV4Variant5 |     | 100  | 100 | 682.016kb | 7.671ms  | ±5.68%   |
| MonoBench  | benchUuidV4Variant6 |     | 100  | 100 | 682.016kb | 5.895ms  | ±5.28%   |
| MonoBench  | benchUuidV4Variant7 |     | 100  | 100 | 682.016kb | 3.893ms  | ±7.42%   |
+------------+---------------------+-----+------+-----+-----------+----------+----------+
```

## 3v4l.org

### Before (https://3v4l.org/Rv7gv)

```
Finding entry points
Branch analysis from position: 0
1 jumps found. (Code = 62) Position 1 = -2
filename:       /in/Rv7gv
function name:  (null)
number of ops:  25
compiled vars:  !0 = $uuid
line      #* E I O op       
```

### After (https://3v4l.org/sht93)

```
Finding entry points
Branch analysis from position: 0
1 jumps found. (Code = 62) Position 1 = -2
filename:       /in/sht93
function name:  (null)
number of ops:  24
compiled vars:  !0 = $uuid
line      #* E I O op  
```