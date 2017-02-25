# normalize_to_utf8_chars
PHP function to normalize / fix / cleanup previously mangled data by encoding problems (e.g. ISO-8859-1 vs Windows-1252). Released under MIT license : use / change / adept whatever, whenever you want - or do not use at all :-)


Function usable when normalizing input data, that has been mangled before, by :

  1) Encoding Problem : Treating UTF-8 Bytes as Windows-1252 or ISO-8859-1
  2) Encoding Problem : Incorrect Double Mis-Conversion
  3) Encoding Problem : ISO-8859-1 vs Windows-1252

The problems should be tackled at the source of the problem of course
(copy/pasting from Microsoft Word comes to mind, for instance), but a lot of
times you get data handed to you when you cannot exercise that control, but
you still will have to fix the issue in the data.

The function does not deal with every possibility, but is pretty extensive and
has grown out of real-world issues I encountered, that actually needed to be
dealt with.

I wrote it in a clear readable form, optimized for a line-length of 120 chars,
but when using a smaller viewing window (e.g. 80 chars), all the most important
information is still visible without scrolling the view, while trying to
keep my personal usual coding style intact, to keep everything readable.
In this case information, went over style (but not in a bad way).

Take notice of the special remarks at the end of the function.
This is where I basically break the Unicode standard, for reasons the situation
usually dictates. You might want to take a different approach however.

The array is maintained in order of the character's Windows 1252 hex-code, for
the sole simple reason that ascii tables (which I used to look up information)
always are.

Written in PHP since that gives me flexibility to deploy it in a quick-n-dirty
shell script to prepare and parse existing data before loading it into a db,
as well in an online service where an existing mangled data stream needs fixing
before further processing / storage in a database (UTF-8).

Again : this should NOT be your solution - this is for fixing 'what-already-is'.

Author: OldskoolOrion (H.Coenen)
