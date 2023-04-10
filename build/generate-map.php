<?php
// Build the mapping array of hex unicode code point lists to shortnames.

$emoji_data = file_get_contents('https://unicode.org/Public/emoji/15.0/emoji-test.txt');
preg_match_all("/^(?<hex>[0-9A-F\s]+)\;\s+(?<qualified>[a-z\-]+)\s+# (?<emoji>.+?)\sE[0-9\.]+?\s(?<name>.+?)$/m", $emoji_data, $emojis);

function formatHex($hex) {
  $hex = trim($hex);
  $hex = str_replace(" ", "-", $hex);
  return $hex;
}

function formatName($name) {
  $name = trim($name);
  $name = str_replace(" ", "_", $name);
  $name = strtolower($name);
  return $name;
}

$map = [];
foreach ($emojis["emoji"] as $key => $value) {
  $hex = formatHex($emojis["hex"][$key]);
  $name = formatName($emojis["name"][$key]);
  $qualified = $emojis["qualified"][$key];
  $map[(string)$hex] = $name;
}

file_put_contents(dirname(__FILE__).'/../src/map.json', json_encode($map, JSON_PRETTY_PRINT));

$keys = array_keys($map);

usort($keys,function($a,$b){
    return strlen($b)-strlen($a);
});

$chunks = array_chunk($keys, round(count($keys) / 2));
$regex1 = preg_replace('/\-?([0-9a-f]+)/i', '\x{$1}', implode('|', $chunks[0]));
$regex2 = preg_replace('/\-?([0-9a-f]+)/i', '\x{$1}', implode('|', $chunks[1]));

file_put_contents(dirname(__FILE__).'/../src/regexp1.json', json_encode($regex1));
file_put_contents(dirname(__FILE__).'/../src/regexp2.json', json_encode($regex2));