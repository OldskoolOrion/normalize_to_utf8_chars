<?

/*

URL : https://github.com/OldskoolOrion/normalize_to_utf8_chars

Function usable when normalizing input data, that has been mangled before, by :

  1) Encoding Problem : Treating UTF-8 Bytes as Windows-1252 or ISO-8859-1
  2) Encoding Problem : Incorrect Double Mis-Conversion
  3) Encoding Problem : ISO-8859-1 vs Windows-1252

The problems should be tackled at the source of the problem of course
(copying/pasting from Microsoft Word comes to mind, for instance), but a lot of
times you get data handed to you when you cannot exercise that control, but
you still will have to fix the issue in the data.

The function does not deal with every possibility, but is pretty extensive and
has grown out of real-world issues I encountered, that actually needed to be
dealt with.

I wrote it in a clear readable form, optimized for a line-length of 120 chars,
but when using a smaller viewing window (e.g. 80 chars), all the most important
information is still visible without scrolling the view, while trying to
keep my personal usual coding style intact (to keep everything readable).
In this case information prevailed over style.

Take notice of the special remarks below the array (marked by [nr]).
For these remarks I basically break the Unicode standard, for reasons the
situation usually dictates. You might want to take a different approach.
That is up to yourself and the situation you want to use (part of) this code for.

The array is maintained in order of the character's Windows 1252 hex-code, for
the sole simple reason that ascii tables (which I used to look up information)
always are.

Written in PHP since that gives me flexibility to deploy it in a quick-n-dirty
shell script to prepare and parse existing data before loading it into a db,
as well in an online service where an existing mangled data stream needs fixing
before further processing / storage in a database (UTF-8).

Again : this should NOT be your solution - this is for fixing 'what-already-is'.

Author: OldskoolOrion (H.Coenen)
*/

function normalize_to_utf8_chars($string) {     // Nr. | Unicode | Win1252 | Expected  | Actually  | UTF8 Bytes
                                                //----------------------------------------------------------------------
  $search=array(chr(0xE2).chr(0x82).chr(0xAC),  // 001 | U+20AC  | 0x80    | €         | â‚¬       | %E2 %82 %AC
                chr(0xE2).chr(0x80).chr(0x9A),  // 002 | U+201A  | 0x82    | ‚         | â€š       | %E2 %80 %9A
                chr(0xC6).chr(0x92),            // 003 | U+0192  | 0x83    | ƒ         | Æ’        | %C6 %92
                chr(0xE2).chr(0x80).chr(0x9E),  // 004 | U+201E  | 0x84    | „         | â€ž       | %E2 %80 %9E
                chr(0xE2).chr(0x80).chr(0xA6),  // 005 | U+2026  | 0x85    | …         | â€¦       | %E2 %80 %A6
                chr(0xE2).chr(0x80).chr(0xA0),  // 006 | U+2020  | 0x86    | †         | â€        | %E2 %80 %A0
                chr(0xE2).chr(0x80).chr(0xA1),  // 007 | U+2021  | 0x87    | ‡         | â€¡       | %E2 %80 %A1
                chr(0xCB).chr(0x86),            // 008 | U+02C6  | 0x88    | ˆ         | Ë†        | %CB %86
                chr(0xE2).chr(0x80).chr(0xB0),  // 009 | U+2030  | 0x89    | ‰         | â€°       | %E2 %80 %B0
                chr(0xC5).chr(0xA0),            // 010 | U+0160  | 0x8A    | Š         | Å         | %C5 %A0
                chr(0xE2).chr(0x80).chr(0xB9),  // 011 | U+2039  | 0x8B    | ‹         | â€¹       | %E2 %80 %B9
                chr(0xC5).chr(0x92),            // 012 | U+0152  | 0x8C    | Œ         | Å’        | %C5 %92
                chr(0xC5).chr(0xBD),            // 013 | U+017D  | 0x8E    | Ž         | Å½        | %C5 %BD
                chr(0xE2).chr(0x80).chr(0x98),  // 014 | U+2018  | 0x91    | ‘         | â€˜       | %E2 %80 %98
                chr(0xE2).chr(0x80).chr(0x99),  // 015 | U+2019  | 0x92    | ’         | â€™       | %E2 %80 %99
                chr(0xE2).chr(0x80).chr(0x9C),  // 016 | U+201C  | 0x93    | “         | â€œ       | %E2 %80 %9C
                chr(0xE2).chr(0x80).chr(0x9D),  // 017 | U+201D  | 0x94    | ”         | â€        | %E2 %80 %9D
                chr(0xE2).chr(0x80).chr(0xA2),  // 018 | U+2022  | 0x95    | •         | â€¢       | %E2 %80 %A2
                chr(0xE2).chr(0x80).chr(0x93),  // 019 | U+2013  | 0x96    | –         | â€“       | %E2 %80 %93  (see: [1])
                chr(0xE2).chr(0x80).chr(0x94),  // 020 | U+2014  | 0x97    | —         | â€”       | %E2 %80 %94  (see: [2])
                chr(0xCB).chr(0x9C),            // 021 | U+02DC  | 0x98    | ˜         | Ëœ        | %CB %9C
                chr(0xE2).chr(0x84).chr(0xA2),  // 022 | U+2122  | 0x99    | ™         | â„¢       | %E2 %84 %A2
                chr(0xC5).chr(0xA1),            // 023 | U+0161  | 0x9A    | š         | Å¡        | %C5 %A1
                chr(0xE2).chr(0x80).chr(0xBA),  // 024 | U+203A  | 0x9B    | ›         | â€º       | %E2 %80 %BA
                chr(0xC5).chr(0x93),            // 025 | U+0153  | 0x9C    | œ         | Å“        | %C5 %93
                chr(0xC5).chr(0xBE),            // 026 | U+017E  | 0x9E    | ž         | Å¾        | %C5 %BE
                chr(0xC5).chr(0xB8),            // 027 | U+0178  | 0x9F    | Ÿ         | Å¸        | %C5 %B8
                chr(0xC2).chr(0xA0),            // 028 | U+00A0  | 0xA0    |           | Â         | %C2 %A0      (see [3])
                chr(0xC2).chr(0xA1),            // 029 | U+00A1  | 0xA1    | ¡         | Â¡        | %C2 %A1
                chr(0xC2).chr(0xA2),            // 030 | U+00A2  | 0xA2    | ¢         | Â¢        | %C2 %A2
                chr(0xC2).chr(0xA3),            // 031 | U+00A3  | 0xA3    | £         | Â£        | %C2 %A3
                chr(0xC2).chr(0xA4),            // 032 | U+00A4  | 0xA4    | ¤         | Â¤        | %C2 %A4
                chr(0xC2).chr(0xA5),            // 033 | U+00A5  | 0xA5    | ¥         | Â¥        | %C2 %A5
                chr(0xC2).chr(0xA6),            // 034 | U+00A6  | 0xA6    | ¦         | Â¦        | %C2 %A6
                chr(0xC2).chr(0xA7),            // 035 | U+00A7  | 0xA7    | §         | Â§        | %C2 %A7
                chr(0xC2).chr(0xA8),            // 036 | U+00A8  | 0xA8    | ¨         | Â¨        | %C2 %A8
                chr(0xC2).chr(0xA9),            // 037 | U+00A9  | 0xA9    | ©         | Â©        | %C2 %A9
                chr(0xC2).chr(0xAA),            // 038 | U+00AA  | 0xAA    | ª         | Âª        | %C2 %AA
                chr(0xC2).chr(0xAB),            // 039 | U+00AB  | 0xAB    | «         | Â«        | %C2 %AB
                chr(0xC2).chr(0xAC),            // 040 | U+00AC  | 0xAC    | ¬         | Â¬        | %C2 %AC
                chr(0xC2).chr(0xAD),            // 041 | U+00AD  | 0xAD    |           | Â         | %C2 %AD      (see: [4])
                chr(0xC2).chr(0xAE),            // 042 | U+00AE  | 0xAE    | ®         | Â®        | %C2 %AE
                chr(0xC2).chr(0xAF),            // 043 | U+00AF  | 0xAF    | ¯         | Â¯        | %C2 %AF
                chr(0xC2).chr(0xB0),            // 044 | U+00B0  | 0xB0    | °         | Â°        | %C2 %B0
                chr(0xC2).chr(0xB1),            // 045 | U+00B1  | 0xB1    | ±         | Â±        | %C2 %B1
                chr(0xC2).chr(0xB2),            // 046 | U+00B2  | 0xB2    | ²         | Â²        | %C2 %B2
                chr(0xC2).chr(0xB3),            // 047 | U+00B3  | 0xB3    | ³         | Â³        | %C2 %B3
                chr(0xC2).chr(0xB4),            // 048 | U+00B4  | 0xB4    | ´         | Â´        | %C2 %B4
                chr(0xC2).chr(0xB5),            // 049 | U+00B5  | 0xB5    | µ         | Âµ        | %C2 %B5
                chr(0xC2).chr(0xB6),            // 050 | U+00B6  | 0xB6    | ¶         | Â¶        | %C2 %B6
                chr(0xC2).chr(0xB7),            // 051 | U+00B7  | 0xB7    | ·         | Â·        | %C2 %B7
                chr(0xC2).chr(0xB8),            // 052 | U+00B8  | 0xB8    | ¸         | Â¸        | %C2 %B8
                chr(0xC2).chr(0xB9),            // 053 | U+00B9  | 0xB9    | ¹         | Â¹        | %C2 %B9
                chr(0xC2).chr(0xBA),            // 054 | U+00BA  | 0xBA    | º         | Âº        | %C2 %BA
                chr(0xC2).chr(0xBB),            // 055 | U+00BB  | 0xBB    | »         | Â»        | %C2 %BB
                chr(0xC2).chr(0xBC),            // 056 | U+00BC  | 0xBC    | ¼         | Â¼        | %C2 %BC
                chr(0xC2).chr(0xBD),            // 057 | U+00BD  | 0xBD    | ½         | Â½        | %C2 %BD
                chr(0xC2).chr(0xBE),            // 058 | U+00BE  | 0xBE    | ¾         | Â¾        | %C2 %BE
                chr(0xC2).chr(0xBF),            // 059 | U+00BF  | 0xBF    | ¿         | Â¿        | %C2 %BF
                chr(0xC3).chr(0x80),            // 060 | U+00C0  | 0xC0    | À         | Ã€        | %C3 %80
                chr(0xC3).chr(0x81),            // 061 | U+00C1  | 0xC1    | Á         | Ã         | %C3 %81
                chr(0xC3).chr(0x82),            // 062 | U+00C2  | 0xC2    | Â         | Ã‚        | %C3 %82
                chr(0xC3).chr(0x83),            // 063 | U+00C3  | 0xC3    | Ã         | Ãƒ        | %C3 %83
                chr(0xC3).chr(0x84),            // 064 | U+00C4  | 0xC4    | Ä         | Ã„        | %C3 %84
                chr(0xC3).chr(0x85),            // 065 | U+00C5  | 0xC5    | Å         | Ã…        | %C3 %85
                chr(0xC3).chr(0x86),            // 066 | U+00C6  | 0xC6    | Æ         | Ã†        | %C3 %86
                chr(0xC3).chr(0x87),            // 067 | U+00C7  | 0xC7    | Ç         | Ã‡        | %C3 %87
                chr(0xC3).chr(0x88),            // 068 | U+00C8  | 0xC8    | È         | Ãˆ        | %C3 %88
                chr(0xC3).chr(0x89),            // 069 | U+00C9  | 0xC9    | É         | Ã‰        | %C3 %89
                chr(0xC3).chr(0x8A),            // 070 | U+00CA  | 0xCA    | Ê         | ÃŠ        | %C3 %8A
                chr(0xC3).chr(0x8B),            // 071 | U+00CB  | 0xCB    | Ë         | Ã‹        | %C3 %8B
                chr(0xC3).chr(0x8C),            // 072 | U+00CC  | 0xCC    | Ì         | ÃŒ        | %C3 %8C
                chr(0xC3).chr(0x8D),            // 073 | U+00CD  | 0xCD    | Í         | Ã         | %C3 %8D
                chr(0xC3).chr(0x8E),            // 074 | U+00CE  | 0xCE    | Î         | ÃŽ        | %C3 %8E
                chr(0xC3).chr(0x8F),            // 075 | U+00CF  | 0xCF    | Ï         | Ã         | %C3 %8F
                chr(0xC3).chr(0x90),            // 076 | U+00D0  | 0xD0    | Ð         | Ã         | %C3 %90
                chr(0xC3).chr(0x91),            // 077 | U+00D1  | 0xD1    | Ñ         | Ã‘        | %C3 %91
                chr(0xC3).chr(0x92),            // 078 | U+00D2  | 0xD2    | Ò         | Ã’        | %C3 %92
                chr(0xC3).chr(0x93),            // 079 | U+00D3  | 0xD3    | Ó         | Ã“        | %C3 %93
                chr(0xC3).chr(0x94),            // 080 | U+00D4  | 0xD4    | Ô         | Ã”        | %C3 %94
                chr(0xC3).chr(0x95),            // 081 | U+00D5  | 0xD5    | Õ         | Ã•        | %C3 %95
                chr(0xC3).chr(0x96),            // 082 | U+00D6  | 0xD6    | Ö         | Ã–        | %C3 %96
                chr(0xC3).chr(0x97),            // 083 | U+00D7  | 0xD7    | ×         | Ã—        | %C3 %97
                chr(0xC3).chr(0x98),            // 084 | U+00D8  | 0xD8    | Ø         | Ã˜        | %C3 %98
                chr(0xC3).chr(0x99),            // 085 | U+00D9  | 0xD9    | Ù         | Ã™        | %C3 %99
                chr(0xC3).chr(0x9A),            // 086 | U+00DA  | 0xDA    | Ú         | Ãš        | %C3 %9A
                chr(0xC3).chr(0x9B),            // 087 | U+00DB  | 0xDB    | Û         | Ã›        | %C3 %9B
                chr(0xC3).chr(0x9C),            // 088 | U+00DC  | 0xDC    | Ü         | Ãœ        | %C3 %9C
                chr(0xC3).chr(0x9D),            // 089 | U+00DD  | 0xDD    | Ý         | Ã         | %C3 %9D
                chr(0xC3).chr(0x9E),            // 090 | U+00DE  | 0xDE    | Þ         | Ãž        | %C3 %9E
                chr(0xC3).chr(0x9F),            // 091 | U+00DF  | 0xDF    | ß         | ÃŸ        | %C3 %9F
                chr(0xC3).chr(0xA0),            // 092 | U+00E0  | 0xE0    | à         | Ã         | %C3 %A0
                chr(0xC3).chr(0xA1),            // 093 | U+00E1  | 0xE1    | á         | Ã¡        | %C3 %A1
                chr(0xC3).chr(0xA2),            // 094 | U+00E2  | 0xE2    | â         | Ã¢        | %C3 %A2
                chr(0xC3).chr(0xA3),            // 095 | U+00E3  | 0xE3    | ã         | Ã£        | %C3 %A3
                chr(0xC3).chr(0xA4),            // 096 | U+00E4  | 0xE4    | ä         | Ã¤        | %C3 %A4
                chr(0xC3).chr(0xA5),            // 097 | U+00E5  | 0xE5    | å         | Ã¥        | %C3 %A5
                chr(0xC3).chr(0xA6),            // 098 | U+00E6  | 0xE6    | æ         | Ã¦        | %C3 %A6
                chr(0xC3).chr(0xA7),            // 099 | U+00E7  | 0xE7    | ç         | Ã§        | %C3 %A7
                chr(0xC3).chr(0xA8),            // 100 | U+00E8  | 0xE8    | è         | Ã¨        | %C3 %A8
                chr(0xC3).chr(0xA9),            // 001 | U+00E9  | 0xE9    | é         | Ã©        | %C3 %A9
                chr(0xC3).chr(0xAA),            // 002 | U+00EA  | 0xEA    | ê         | Ãª        | %C3 %AA
                chr(0xC3).chr(0xAB),            // 003 | U+00EB  | 0xEB    | ë         | Ã«        | %C3 %AB
                chr(0xC3).chr(0xAC),            // 004 | U+00EC  | 0xEC    | ì         | Ã¬        | %C3 %AC
                chr(0xC3).chr(0xAD),            // 005 | U+00ED  | 0xED    | í         | Ã         | %C3 %AD
                chr(0xC3).chr(0xAE),            // 006 | U+00EE  | 0xEE    | î         | Ã®        | %C3 %AE
                chr(0xC3).chr(0xAF),            // 007 | U+00EF  | 0xEF    | ï         | Ã¯        | %C3 %AF
                chr(0xC3).chr(0xB0),            // 008 | U+00F0  | 0xF0    | ð         | Ã°        | %C3 %B0
                chr(0xC3).chr(0xB1),            // 009 | U+00F1  | 0xF1    | ñ         | Ã±        | %C3 %B1
                chr(0xC3).chr(0xB2),            // 000 | U+00F2  | 0xF2    | ò         | Ã²        | %C3 %B2
                chr(0xC3).chr(0xB3),            // 001 | U+00F3  | 0xF3    | ó         | Ã³        | %C3 %B3
                chr(0xC3).chr(0xB4),            // 002 | U+00F4  | 0xF4    | ô         | Ã´        | %C3 %B4
                chr(0xC3).chr(0xB5),            // 003 | U+00F5  | 0xF5    | õ         | Ãµ        | %C3 %B5
                chr(0xC3).chr(0xB6),            // 004 | U+00F6  | 0xF6    | ö         | Ã¶        | %C3 %B6
                chr(0xC3).chr(0xB7),            // 005 | U+00F7  | 0xF7    | ÷         | Ã·        | %C3 %B7
                chr(0xC3).chr(0xB8),            // 006 | U+00F8  | 0xF8    | ø         | Ã¸        | %C3 %B8
                chr(0xC3).chr(0xB9),            // 007 | U+00F9  | 0xF9    | ù         | Ã¹        | %C3 %B9
                chr(0xC3).chr(0xBA),            // 008 | U+00FA  | 0xFA    | ú         | Ãº        | %C3 %BA
                chr(0xC3).chr(0xBB),            // 009 | U+00FB  | 0xFB    | û         | Ã»        | %C3 %BB
                chr(0xC3).chr(0xBC),            // 000 | U+00FC  | 0xFC    | ü         | Ã¼        | %C3 %BC
                chr(0xC3).chr(0xBD),            // 001 | U+00FD  | 0xFD    | ý         | Ã½        | %C3 %BD
                chr(0xC3).chr(0xBE),            // 002 | U+00FE  | 0xFE    | þ         | Ã¾        | %C3 %BE
                chr(0xC3).chr(0xBF));           // 003 | U+00FF  | 0xFF    | ÿ         | Ã¿        | %C3 %BF

                // [1] : Unicode dictates 'En dash'. Replaced by space minus space (' - ').
                // [2] : Unicode dictates 'Em dash'. Replaced by space minus space (' - ').
                // [3] : Unicode dictates 'Non breaking space' : Replaced by a single space (' ').
                // [4] : Unicode dictates 'Soft hyphen' : Replaced by a single space (' ').
                // See https://github.com/OldskoolOrion/normalize_to_utf8_chars for a more verbose explenation.

  $replace = array('€', '‚', 'ƒ', '„', '…', '†', '‡', 'ˆ', '‰', 'Š', '‹', 'Œ', 'Ž', '‘', '’', '“', '”', '•', ' - ',
                   ' - ', '˜', '™', 'š', '›', 'œ', 'ž', 'Ÿ', ' ', '¡', '¢', '£', '¤', '¥', '¦', '§', '¨', '©', 'ª',
                   '«', '¬', ' ', '®', '¯', '°', '±', '²', '³', '´', 'µ', '¶', '·', '¸', '¹', 'º', '»', '¼', '½',
                   '¾', '¿', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
                   'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', '×', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß', 'à', 'á', 'â', 'ã',
                   'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö',
                   '÷', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ');

  return str_replace($search, $replace, $string);
}

?>
