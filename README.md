# Symfony UuidV4 Optimization & Performances

This change enhances the generation of the 19th character in UUIDv4 in the Symfony UuidV4 class. 

https://github.com/symfony/symfony/pull/54027


# Variants

| Benchmark                | Description                                                                       | Implementation                                                            |
|--------------------------|-----------------------------------------------------------------------------------|---------------------------------------------------------------------------|
| `benchUuidV4Current()`   | Replaces the 19th character with a mapping based on its current value.           | A mapping array is used to determine the replacement for the 19th character. |
| `benchUuidV4Variant1()`  | Uses the least significant 2 bits of the first character to select the replacement for the 19th character. | The first character is used to determine the replacement based on its 2 least significant bits. |
| `benchUuidV4Variant3()`  | Utilizes mathematical operations on the ASCII value of the 19th character for replacement. | The ASCII value is modified and transformed into a character based on a specific formula. |
| `benchUuidV4Variant4()`  | Applies bitwise and mathematical operations to the ASCII value of the 19th character for replacement. | Bitwise operations and mathematical transformations are performed on the ASCII value to determine the replacement. |
| `benchUuidV4Variant5()`  | Randomly selects a replacement for the 19th character.                             | A random replacement is chosen from the available options.                |
| `benchUuidV4Variant6()`  | Uses the current timestamp modulo 4 to determine the replacement for the 19th character. | The current timestamp's remainder after division by 4 is used to select the replacement. |


## General Notes

- All variants iterate over a set of 50 pre-defined UUIDs for testing purposes.
- The 19th character replacement is performed in a loop for each UUID in the set.
- Variants are primarily based on character manipulation or random selection.
- These benchmarks aim to evaluate the performance of different strategies for generating UUIDv4 values with a focus on the 19th character.

## Code

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