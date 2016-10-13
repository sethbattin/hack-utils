<?hh // strict

namespace HackUtils;

function _assert_equal<T>(T $actual, T $expected): void {
  if ($actual !== $expected) {
    throw new \Exception(
      \sprintf(
        "Expected %s, got %s",
        \var_export($expected, true),
        \var_export($actual, true),
      ),
    );
  }
}

function _run_tests(): void {
  _assert_equal(to_hex("\x00\xff\x20"), "00ff20");
  _assert_equal(from_hex("00ff20"), "\x00\xff\x20");
  _assert_equal(from_hex("00Ff20"), "\x00\xff\x20");

  _assert_equal(length(str_shuffle("abc")), 3);

  _assert_equal(reverse_string("abc"), 'cba');
  _assert_equal(reverse_string(""), '');

  _assert_equal(to_lower("ABC.1.2.3"), "abc.1.2.3");
  _assert_equal(to_upper("abc.1.2.3"), "ABC.1.2.3");

  _assert_equal(split(''), []);
  _assert_equal(split('a'), ['a']);
  _assert_equal(split('abc'), ['a', 'b', 'c']);

  _assert_equal(split('', '', 1), []);
  _assert_equal(split('a', '', 1), ['a']);
  _assert_equal(split('abc', '', 1), ['abc']);
  _assert_equal(split('abc', '', 2), ['a', 'bc']);
  _assert_equal(split('abc', '', 3), ['a', 'b', 'c']);

  _assert_equal(split('', 'b'), ['']);
  _assert_equal(split('abc', 'b'), ['a', 'c']);

  _assert_equal(split('abc', 'b', 1), ['abc']);
  _assert_equal(split('abc', 'b', 2), ['a', 'c']);

  _assert_equal(chunk_string('abc', 1), ['a', 'b', 'c']);
  _assert_equal(chunk_string('abc', 2), ['ab', 'c']);
  _assert_equal(chunk_string('abc', 3), ['abc']);

  _assert_equal(join([]), '');
  _assert_equal(join(['abc']), 'abc');
  _assert_equal(join(['a', 'bc']), 'abc');

  _assert_equal(join([], ','), '');
  _assert_equal(join(['abc'], ','), 'abc');
  _assert_equal(join(['a', 'bc'], ','), 'a,bc');

  _assert_equal(replace_count('abc', 'b', 'lol'), tuple('alolc', 1));
  _assert_equal(replace_count('abc', 'B', 'lol'), tuple('abc', 0));
  _assert_equal(replace_count('abc', 'B', 'lol', true), tuple('alolc', 1));

  _assert_equal(splice('abc', 1, 1), 'ac');
  _assert_equal(splice('abc', 1, 1, 'lol'), 'alolc');

  _assert_equal(slice('abc', 1, 1), 'b');
  _assert_equal(slice('abc', -1, 1), 'c');
  _assert_equal(slice('abc', 1, -1), 'b');
  _assert_equal(slice('abc', 1), 'bc');
  _assert_equal(slice('abc', -1), 'c');

  _assert_equal(pad('abc', 3), 'abc');
  _assert_equal(pad('abc', 4), 'abc ');
  _assert_equal(pad('abc', 5), ' abc ');
  _assert_equal(pad('abc', 6), ' abc  ');
  _assert_equal(pad('1', 3, 'ab'), 'a1a');
  _assert_equal(pad('1', 4, 'ab'), 'a1ab');

  _assert_equal(pad_left('abc', 3), 'abc');
  _assert_equal(pad_left('abc', 4), ' abc');
  _assert_equal(pad_left('abc', 5), '  abc');
  _assert_equal(pad_left('abc', 6), '   abc');
  _assert_equal(pad_left('1', 3, 'ab'), 'ab1');
  _assert_equal(pad_left('1', 4, 'ab'), 'aba1');

  _assert_equal(pad_right('abc', 3), 'abc');
  _assert_equal(pad_right('abc', 4), 'abc ');
  _assert_equal(pad_right('abc', 5), 'abc  ');
  _assert_equal(pad_right('abc', 6), 'abc   ');
  _assert_equal(pad_right('1', 3, 'ab'), '1ab');
  _assert_equal(pad_right('1', 4, 'ab'), '1aba');

  _assert_equal(str_repeat('123', 3), '123123123');

  _assert_equal(from_char_code(128), "\x80");
  _assert_equal(from_char_code(0), "\x00");
  _assert_equal(from_char_code(255), "\xFF");

  _assert_equal(char_code_at('a'), 97);
  _assert_equal(char_code_at('a99'), 97);

  _assert_equal(str_cmp('a', 'a'), 0);
  _assert_equal(str_cmp('a', 'A'), 1);
  _assert_equal(str_cmp('', ''), 0);
  _assert_equal(str_cmp('', 'a'), -1);
  _assert_equal(str_cmp('a', ''), 1);

  _assert_equal(str_cmp('a', 'a', true), 0);
  _assert_equal(str_cmp('a', 'A', true), 0);
  _assert_equal(str_cmp('', '', true), 0);
  _assert_equal(str_cmp('', 'a', true), -1);
  _assert_equal(str_cmp('a', '', true), 1);

  _assert_equal(str_eq('a', 'a'), true);
  _assert_equal(str_eq('a', 'A'), false);
  _assert_equal(str_eq('', ''), true);
  _assert_equal(str_eq('', 'a'), false);
  _assert_equal(str_eq('a', ''), false);

  _assert_equal(str_eq('a', 'a', true), true);
  _assert_equal(str_eq('a', 'A', true), true);
  _assert_equal(str_eq('', '', true), true);
  _assert_equal(str_eq('', 'a', true), false);
  _assert_equal(str_eq('a', '', true), false);

  _assert_equal(find('a', 'a'), 0);
  _assert_equal(find('a', 'a', 1), null);
  _assert_equal(find('a', 'a', -1), 0);
  _assert_equal(find('abc', 'a'), 0);
  _assert_equal(find('abc', 'b'), 1);
  _assert_equal(find('abc', 'c'), 2);
  _assert_equal(find('abc', 'a', -2), null);
  _assert_equal(find('abc', 'b', -2), 1);
  _assert_equal(find('abc', 'c', -2), 2);
  _assert_equal(find('abbb', 'bb'), 1);
  _assert_equal(find('abbb', 'bb', 2), 2);

  _assert_equal(find_last('a', 'a'), 0);
  _assert_equal(find_last('a', 'a', 1), null);
  _assert_equal(find_last('a', 'a', -1), 0);
  _assert_equal(find_last('aba', 'a'), 2);
  _assert_equal(find_last('aba', 'b'), 1);
  _assert_equal(find_last('aba', 'c'), null);
  _assert_equal(find_last('aba', 'a', -2), 2);
  _assert_equal(find_last('aba', 'b', -2), 1);
  _assert_equal(find_last('aba', 'c', -2), null);
  _assert_equal(find_last('abbb', 'bb'), 2);
  _assert_equal(find_last('abbb', 'bb', 2), 2);

  _assert_equal(ends_with('abbb', 'bb'), true);
  _assert_equal(ends_with('abbb', 'ba'), false);
  _assert_equal(ends_with('abbb', ''), true);
  _assert_equal(ends_with('', ''), true);
  _assert_equal(ends_with('', 'a'), false);

  _assert_equal(starts_with('abbb', 'ab'), true);
  _assert_equal(starts_with('abbb', 'bb'), false);
  _assert_equal(starts_with('abbb', ''), true);
  _assert_equal(starts_with('', ''), true);
  _assert_equal(starts_with('', 'a'), false);

  print "okay\n";
}
