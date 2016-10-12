<?hh // strict

namespace HackUtils;

type datetime = DateTime\datetime;
type timezone = DateTime\timezone;

type key = arraykey;
type map<+T> = array<key, T>;

type fn0<+T> = (function(): T);
type fn1<-T1, +T> = (function(T1): T);
type fn2<-T1, -T2, +T> = (function(T1, T2): T);
type fn3<-T1, -T2, -T3, +T> = (function(T1, T2, T3): T);
type fn4<-T1, -T2, -T3, -T4, +T> = (function(T1, T2, T3, T4): T);
type fn5<-T1, -T2, -T3, -T4, -T5, +T> = (function(T1, T2, T3, T4, T5): T);

/**
 * The Hack typechecker reports "null" as "Partially type checked code.
 * Consider adding type annotations". To avoid that, you can replace it with
 * a call to this function.
 */
function new_null<T>(): ?T {
  return null;
}

/**
 * Convert a nullable value into a non-nullable value, throwing an exception
 * in the case of null.
 */
function null_throws<T>(?T $value, string $message = "Unexpected null"): T {
  return $value === null ? throw_(new \Exception($message)) : $value;
}

/**
 * Throw an exception in the context of an expression.
 */
function throw_<T>(\Exception $e): T {
  throw $e;
}

function if_null<T>(?T $x, T $y): T {
  return $x === null ? $y : $x;
}

function fst<T>((T, mixed) $t): T {
  return $t[0];
}

function snd<T>((mixed, T) $t): T {
  return $t[1];
}

interface Gettable<+T> {
  public function get(): T;
}

interface Settable<-T> {
  public function set(T $value): void;
}

/**
 * Simple container for a value of a given type. Useful to replace PHP's
 * built in references, which are not supported in Hack.
 */
final class Ref<T> implements Gettable<T>, Settable<T> {
  public function __construct(private T $value) {}

  public function get(): T {
    return $this->value;
  }

  public function set(T $value): void {
    $this->value = $value;
  }
}

function is_vector(array<mixed, mixed> $x): bool {
  $i = 0;
  foreach ($x as $k => $v) {
    if ($k !== $i++) {
      return false;
    }
  }
  return true;
}

function concat<T>(array<T> $a, array<T> $b): array<T> {
  return \array_merge($a, $b);
}

function concat_all<T>(array<array<T>> $vectors): array<T> {
  return $vectors ? \call_user_func_array('array_merge', $vectors) : [];
}

function push<T>(array<T> $v, T $x): array<T> {
  \array_push($v, $x);
  return $v;
}

function pop<T>(array<T> $v): (array<T>, T) {
  _check_empty($v, 'remove last element');
  $x = \array_pop($v);
  return tuple($v, $x);
}

function unshift<T>(T $x, array<T> $v): array<T> {
  \array_unshift($v, $x);
  return $v;
}

function shift<T>(array<T> $v): (T, array<T>) {
  _check_empty($v, 'remove first element');
  $x = \array_shift($v);
  return tuple($x, $v);
}

function _check_empty(array<mixed> $a, string $op): void {
  if (!$a) {
    throw new \Exception("Cannot $op: Array is empty");
  }
}

function range(int $start, int $end, int $step = 1): array<int> {
  return \range($start, $end, $step);
}

function filter<T>(array<T> $list, (function(T): bool) $f): array<T> {
  $ret = filter_assoc($list, $f);
  // array_filter() preserves keys, so if some elements were removed,
  // renumber keys 0,1...N.
  return count($ret) != count($list) ? values($ret) : $list;
}

function filter_assoc<Tk, Tv>(
  array<Tk, Tv> $map,
  (function(Tv): bool) $f,
): array<Tk, Tv> {
  return \array_filter($map, $f);
}

function map<Tin, Tout>(
  array<Tin> $list,
  (function(Tin): Tout) $f,
): array<Tout> {
  return \array_map($f, $list);
}

function map_assoc<Tk, Tv1, Tv2>(
  array<Tk, Tv1> $map,
  (function(Tv1): Tv2) $f,
): array<Tk, Tv2> {
  return \array_map($f, $map);
}

function reduce<Tin, Tout>(
  array<arraykey, Tin> $list,
  (function(Tout, Tin): Tout) $f,
  Tout $initial,
): Tout {
  return \array_reduce($list, $f, $initial);
}

function reduce_right<Tin, Tout>(
  array<arraykey, Tin> $list,
  (function(Tout, Tin): Tout) $f,
  Tout $value,
): Tout {
  // Messy, but the easiest way of iterating through an array in reverse
  // without creating a copy.
  \end($list);
  while (!\is_null($key = \key($list))) {
    $value = $f($value, \current($list));
    \prev($list);
  }
  return $value;
}

function group_by<Tk as arraykey, Tv>(
  array<mixed, Tv> $a,
  (function(Tv): Tk) $f,
): array<Tk, array<Tv>> {
  $res = [];
  foreach ($a as $v) {
    $res[$f($v)][] = $v;
  }
  return $res;
}

function any<T>(array<mixed, T> $a, (function(T): bool) $f): bool {
  foreach ($a as $x) {
    if ($f($x)) {
      return true;
    }
  }
  return false;
}

function all<T>(array<mixed, T> $a, (function(T): bool) $f): bool {
  foreach ($a as $x) {
    if (!$f($x)) {
      return false;
    }
  }
  return true;
}

function keys_to_lower<Tk, Tv>(array<Tk, Tv> $array): array<Tk, Tv> {
  return \array_change_key_case($array, \CASE_LOWER);
}

function keys_to_uppper<Tk, Tv>(array<Tk, Tv> $array): array<Tk, Tv> {
  return \array_change_key_case($array, \CASE_UPPER);
}

function to_pairs<Tk, Tv>(array<Tk, Tv> $map): array<(Tk, Tv)> {
  $r = [];
  foreach ($map as $k => $v) {
    $r[] = tuple($k, $v);
  }
  return $r;
}

function from_pairs<Tk, Tv>(array<(Tk, Tv)> $pairs): array<Tk, Tv> {
  $r = [];
  foreach ($pairs as $p) {
    $r[$p[0]] = $p[1];
  }
  return $r;
}

function get_key<Tk as arraykey, Tv>(array<Tk, Tv> $map, Tk $key): Tv {
  $res = $map[$key];
  if ($res === null && !key_exists($map, $key)) {
    throw new \Exception("Key '$key' does not exist in map");
  }
  return $res;
}

function set_key<Tk, Tv>(array<Tk, Tv> $map, Tk $key, Tv $val): array<Tk, Tv> {
  $map[$key] = $val;
  return $map;
}

function get_key_or_null<Tk, Tv>(array<Tk, Tv> $map, Tk $key): ?Tv {
  return $map[$key] ?? new_null();
}

function get_key_or_default<Tk, Tv>(
  array<Tk, Tv> $map,
  Tk $key,
  Tv $default,
): Tv {
  return key_exists($map, $key) ? $map[$key] : $default;
}

function key_exists<Tk>(array<Tk, mixed> $map, Tk $key): bool {
  return \array_key_exists($key, $map);
}

function get_offset<T>(array<T> $v, int $i): T {
  $l = \count($v);
  if ($i < 0) {
    $i += $l;
  }
  if ($i < 0 || $i >= $l) {
    throw new \Exception("Index $i out of bounds in array of length $l");
  }
  return $v[$i];
}

function set_offset<T>(array<T> $v, int $i, T $x): array<T> {
  $l = \count($v);
  if ($i < 0) {
    $i += $l;
  }
  if ($i < 0 || $i >= $l) {
    throw new \Exception("Index $i out of bounds in array of length $l");
  }
  $v[$i] = $x;
  return $v;
}

/**
 * The key of a map is actually a string, but PHP converts intish strings to
 * ints. Use this function to convert them back.
 */
function fixkey(arraykey $key): string {
  return $key.'';
}

function fixkeys(array<arraykey> $keys): array<string> {
  return map($keys, $key ==> $key.'');
}

function column<Tk as arraykey, Tv>(
  array<array<Tk, Tv>> $maps,
  Tk $key,
): array<Tv> {
  return \array_column($maps, $key);
}

function combine<Tk, Tv>(array<Tk> $keys, array<Tv> $values): array<Tk, Tv> {
  return \array_combine($keys, $values);
}

function separate<Tk, Tv>(array<Tk, Tv> $map): (array<Tk>, array<Tv>) {
  $ks = [];
  $vs = [];
  foreach ($map as $k => $v) {
    $ks[] = $k;
    $vs[] = $v;
  }
  return tuple($ks, $vs);
}

function from_keys<Tk, Tv>(array<Tk> $keys, Tv $value): array<Tk, Tv> {
  return \array_fill_keys($keys, $value);
}

function flip<Tk as arraykey, Tv as arraykey>(
  array<Tk, Tv> $map,
): array<Tv, Tk> {
  return \array_flip($map);
}

function flip_count<T as arraykey>(array<arraykey, T> $values): array<T, int> {
  return \array_count_values($values);
}

function keys<Tk>(array<Tk, mixed> $map): array<Tk> {
  return \array_keys($map);
}

function keys_strings(array<arraykey, mixed> $map): array<string> {
  return map(keys($map), $k ==> ''.$k);
}

function values<Tv>(array<mixed, Tv> $map): array<Tv> {
  return \array_values($map);
}

/**
 * If a key exists in both arrays, the value from the second array is used.
 */
function union_keys<Tk, Tv>(
  array<Tk, Tv> $a,
  array<Tk, Tv> $b,
): array<Tk, Tv> {
  return \array_replace($a, $b);
}

/**
 * If a key exists in multiple arrays, the value from the later array is used.
 */
function union_keys_all<Tk, Tv>(array<array<Tk, Tv>> $maps): array<Tk, Tv> {
  return \call_user_func_array('array_replace', $maps);
}

function intersect<T as arraykey>(array<T> $a, array<T> $b): array<T> {
  return \array_values(\array_intersect($a, $b));
}

/**
 * Returns an array with only keys that exist in both arrays, using values from
 * the first array.
 */
function intersect_keys<Tk as arraykey, Tv>(
  array<Tk, Tv> $a,
  array<Tk, Tv> $b,
): array<Tk, Tv> {
  return \array_intersect_key($a, $b);
}

function diff<T as arraykey>(array<T> $a, array<T> $b): array<T> {
  return \array_values(\array_diff($a, $b));
}

/**
 * Returns an array with keys that exist in the first array but not the second,
 * using values from the first array.
 */
function diff_keys<Tk as arraykey, Tv>(
  array<Tk, Tv> $a,
  array<Tk, Tv> $b,
): array<Tk, Tv> {
  return \array_intersect_key($a, $b);
}

/**
 * Extract multiple keys from a map at once.
 */
function select<Tk, Tv>(array<Tk, Tv> $map, array<Tk> $keys): array<Tv> {
  $ret = [];
  foreach ($keys as $key) {
    $ret[] = $map[$key];
  }
  return $ret;
}

function zip<Ta, Tb>(array<Ta> $a, array<Tb> $b): array<(Ta, Tb)> {
  $r = [];
  $l = min(count($a), count($b));
  for ($i = 0; $i < $l; $i++) {
    $r[] = tuple($a[$i], $b[$i]);
  }
  return $r;
}

function zip_assoc<Tk, Ta, Tb>(
  array<Tk, Ta> $a,
  array<Tk, Tb> $b,
): array<Tk, (Ta, Tb)> {
  $ret = [];
  foreach ($a as $k => $v) {
    if (key_exists($b, $k)) {
      $ret[$k] = tuple($v, $b[$k]);
    }
  }
  return $ret;
}

function unzip<Ta, Tb>(array<(Ta, Tb)> $x): (array<Ta>, array<Tb>) {
  $a = [];
  $b = [];
  foreach ($x as $p) {
    $a[] = $p[0];
    $b[] = $p[1];
  }
  return tuple($a, $b);
}

function unzip_assoc<Tk, Ta, Tb>(
  array<Tk, (Ta, Tb)> $map,
): (array<Tk, Ta>, array<Tk, Tb>) {
  $a = [];
  $b = [];
  foreach ($map as $k => $v) {
    $a[$k] = $v[0];
    $b[$k] = $v[1];
  }
  return tuple($a, $b);
}

function shuffle<T>(array<T> $list): array<T> {
  \shuffle($list);
  return $list;
}

function shuffle_string(string $string): string {
  return \str_shuffle($string);
}

function reverse<T>(array<T> $list): array<T> {
  return \array_reverse($list, false);
}

function reverse_assoc<Tk, Tv>(array<Tk, Tv> $map): array<Tk, Tv> {
  return \array_reverse($map, true);
}

function reverse_string(string $string): string {
  return \strrev($string);
}

function chunk<T>(array<T> $list, int $size): array<array<T>> {
  if ($size < 1) {
    throw new \Exception("Chunk size must be >= 1");
  }
  return \array_chunk($list, $size, false);
}

function chunk_assoc<Tk, Tv>(
  array<Tk, Tv> $map,
  int $size,
): array<array<Tk, Tv>> {
  if ($size < 1) {
    throw new \Exception("Chunk size must be >= 1");
  }
  return \array_chunk($map, $size, true);
}

function chunk_string(string $string, int $size): array<string> {
  if ($size < 1) {
    throw new \Exception("Chunk size must be >= 1");
  }
  $ret = \str_split($string, $size);
  if (!\is_array($ret)) {
    throw new \Exception('str_split() failed');
  }
  return $ret;
}

function repeat<T>(T $value, int $count): array<T> {
  return \array_fill(0, $count, $value);
}

function repeat_string(string $string, int $count): string {
  return \str_repeat($string, $count);
}

function slice(string $string, int $offset, ?int $length = null): string {
  $ret = \substr($string, $offset, $length ?? 0x7FFFFFFF);
  // \substr() returns false "on failure".
  return $ret === false ? '' : $ret;
}

function slice_array<T>(
  array<T> $list,
  int $offset,
  ?int $length = null,
): array<T> {
  return \array_slice($list, $offset, $length);
}

function slice_assoc<Tk, Tv>(
  array<Tk, Tv> $map,
  int $offset,
  ?int $length = null,
): array<Tk, Tv> {
  return \array_slice($map, $offset, $length, true);
}

function splice(
  string $string,
  int $offset,
  ?int $length = null,
  string $replacement = '',
): string {
  return
    \substr_replace($string, $replacement, $offset, $length ?? 0x7FFFFFFF);
}

/**
 * Returns a pair of (new list, removed elements).
 */
function splice_array<T>(
  array<T> $list,
  int $offset,
  ?int $length = null,
  array<T> $replacement = [],
): (array<T>, array<T>) {
  $ret = \array_splice($list, $offset, $length, $replacement);
  return tuple($list, $ret);
}

function find(
  string $haystack,
  string $needle,
  int $offset = 0,
  bool $caseInsensitive = false,
): ?int {
  $ret =
    $caseInsensitive
      ? \stripos($haystack, $needle, $offset)
      : \strpos($haystack, $needle, $offset);
  return $ret === false ? null : $ret;
}

function find_last(
  string $haystack,
  string $needle,
  int $offset = 0,
  bool $caseInsensitive = false,
): ?int {
  $ret =
    $caseInsensitive
      ? \strripos($haystack, $needle, $offset)
      : \strrpos($haystack, $needle, $offset);
  return $ret === false ? null : $ret;
}

function find_count(string $haystack, string $needle, int $offset = 0): int {
  return \substr_count($haystack, $needle, $offset);
}

function contains(string $haystack, string $needle, int $offset = 0): bool {
  return find($haystack, $needle, $offset) !== null;
}

function length(string $string): int {
  return \strlen($string);
}

function count(array<mixed, mixed> $array): int {
  return \count($array);
}

function size(array<mixed, mixed> $array): int {
  return \count($array);
}

function find_key<Tk, Tv>(array<Tk, Tv> $array, Tv $value): ?Tk {
  $ret = \array_search($array, $value, true);
  return $ret === false ? new_null() : $ret;
}

function find_keys<Tk, Tv>(array<Tk, Tv> $array, Tv $value): array<Tk> {
  return \array_keys($array, $value, true);
}

function find_last_key<Tk, Tv>(array<Tk, Tv> $array, Tv $value): ?Tk {
  // Messy, but the easiest way to iterator in reverse that works
  // with both vector and associative arrays and doesn't create a copy.
  \end($array);
  while (!\is_null($key = \key($array))) {
    if (\current($array) === $value) {
      return $key;
    }
    \prev($array);
  }
  return new_null();
}

function in<T>(T $value, array<mixed, T> $array): bool {
  return \in_array($value, $array, true);
}

function to_hex(string $string): string {
  return \bin2hex($string);
}

function from_hex(string $string): string {
  $ret = \hex2bin($string);
  if (!\is_string($ret)) {
    throw new \Exception("Invalid hex string: $string");
  }
  return $ret;
}

function to_lower(string $string): string {
  return \strtolower($string);
}

function to_upper(string $string): string {
  return \strtoupper($string);
}

const string SPACE_CHARS = " \t\r\n\v\f";
const string TRIM_CHARS = " \t\r\n\v\x00";

function trim(string $string, string $chars = TRIM_CHARS): string {
  return \trim($string, $chars);
}

function trim_left(string $string, string $chars = TRIM_CHARS): string {
  return \ltrim($string, $chars);
}

function trim_right(string $string, string $chars = TRIM_CHARS): string {
  return \rtrim($string, $chars);
}

function split(
  string $string,
  string $delimiter = '',
  int $limit = 0x7FFFFFFF,
): array<string> {
  if ($limit < 1) {
    throw new \Exception("Limit must be >= 1");
  }
  // \explode() doesn't accept an empty delimiter
  if ($delimiter === '') {
    if ($string === '') {
      // The only case where we return an empty array is if both the delimiter
      // and string are empty, i.e. if they are tring to split the string
      // into characters and the string is empty.
      return [];
    }
    if ($limit == 1) {
      return [$string];
    }
    if (length($string) > $limit) {
      $ret = \str_split(slice($string, 0, $limit - 1));
      $ret[] = slice($string, $limit - 1);
      return $ret;
    }
    return \str_split($string);
  }
  return \explode($delimiter, $string, $limit);
}

/**
 * Split a string into lines terminated by \n or \r\n.
 * A final line terminator is optional.
 */
function split_lines(string $string): array<string> {
  $lines = split($string, "\n");
  // Remove a final \r at the end of any lines
  foreach ($lines as $i => $line) {
    if (slice($line, -1) === "\r") {
      $lines[$i] = slice($line, 0, -1);
    }
  }
  // Remove a final empty line
  if ($lines && $lines[count($lines) - 1] === '') {
    $lines = slice_array($lines, 0, -1);
  }
  return $lines;
}

function join(array<string> $strings, string $delimiter = ''): string {
  return \implode($delimiter, $strings);
}

/**
 * Join lines back together with the given line separator. A final
 * separator is included in the output.
 */
function join_lines(array<string> $lines, string $nl = "\n"): string {
  return $lines ? join($lines, $nl).$nl : '';
}

function replace(
  string $subject,
  string $search,
  string $replace,
  bool $ci = false,
): string {
  $count = 0;
  $result =
    $ci
      ? \str_ireplace($search, $replace, $subject)
      : \str_replace($search, $replace, $subject);
  if (!\is_string($result)) {
    throw new \Exception('str_i?replace() failed');
  }
  return $result;
}

function replace_count(
  string $subject,
  string $search,
  string $replace,
  bool $ci = false,
): (string, int) {
  $count = 0;
  $result =
    $ci
      ? \str_ireplace($search, $replace, $subject, $count)
      : \str_replace($search, $replace, $subject, $count);
  if (!\is_string($result)) {
    throw new \Exception('str_i?replace() failed');
  }
  return tuple($result, $count);
}

function pad(string $string, int $length, string $pad = ' '): string {
  return \str_pad($string, $length, $pad, \STR_PAD_BOTH);
}

function pad_array<T>(array<T> $list, int $size, T $value): array<T> {
  return \array_pad($list, $size, $value);
}

function pad_left(string $string, int $length, string $pad = ' '): string {
  return \str_pad($string, $length, $pad, \STR_PAD_LEFT);
}

function pad_right(string $string, int $length, string $pad = ' '): string {
  return \str_pad($string, $length, $pad, \STR_PAD_RIGHT);
}

function from_char_code(int $ascii): string {
  if ($ascii < 0 || $ascii >= 256) {
    throw new \Exception(
      'ASCII character code must be >= 0 and < 256: '.$ascii,
    );
  }

  return \chr($ascii);
}

function char_at(string $s, int $i = 0): string {
  $l = \strlen($s);
  // Allow caller to specify negative offsets for characters from the end of
  // the string
  if ($i < 0) {
    $i += $l;
  }
  if ($i < 0 || $i >= $l) {
    throw new \Exception(
      "String offset $i out of bounds in string of length $l",
    );
  }
  return $s[$i];
}

function char_code_at(string $string, int $offset = 0): int {
  return \ord(char_at($string, $offset));
}

function str_cmp(
  string $a,
  string $b,
  bool $ci = false,
  bool $natural = false,
): int {
  $ret =
    $ci
      ? ($natural ? \strnatcasecmp($a, $b) : \strcasecmp($a, $b))
      : ($natural ? \strnatcmp($a, $b) : \strcmp($a, $b));
  return sign($ret);
}

function str_eq(
  string $a,
  string $b,
  bool $ci = false,
  bool $natural = false,
): bool {
  return str_cmp($a, $b, $ci, $natural) == 0;
}

function starts_with(string $string, string $prefix): bool {
  return slice($string, 0, length($prefix)) === $prefix;
}

function ends_with(string $string, string $suffix): bool {
  $length = length($suffix);
  return $length ? slice($string, -$length) === $suffix : true;
}