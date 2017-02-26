# normalize_to_utf8_chars

## PHP function to normalize / fix / cleanup previously mangled data by encoding problems (e.g. ISO-8859-1 vs Windows-1252).
#### Released under MIT license : use / change / adept whatever, whenever you want - or do not use at all :-)


Function usable when normalizing input data, that has been mangled before, by :

  - Encoding Problem : Treating UTF-8 Bytes as Windows-1252 or ISO-8859-1
  
  - Encoding Problem : Incorrect Double Mis-Conversion
  
  - Encoding Problem : ISO-8859-1 vs Windows-1252

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
This is where I basically break the Unicode standard, for reasons the situation
usually dictates. You might want to take a different approach.
That is up to yourself and the situation you want to use (part of) this code for.

Non-standard data manipulation :

  - U+2013 'En dash'
    - You expect to see : -
    - You see           : â€“ (UTF-8 Bytes : %E2 %80 %93)
      - replaced by     : Replaced by space minus space (' - '), since (based on my personal
                          real world encounteres in data) the character is usually found
                          when there is a need to break a sentence or data up, so it's clear
                          there are two seperate entities in play. For this reason I added the
                          spaces before as well behind the replacing character.

  - U+2014 'Em dash'
    - You expect to see : -
    - You see           : â€” (UTF-8 Bytes : %E2 %80 %94)
      - replaced by     : Replaced by space minus space (' - '), since (based on my personal
                          real world encounteres in data) the character is usually found
                          when there is a need to break a sentence or data up, so it's clear
                          there are two seperate entities in play. For this reason I added the
                          spaces before as well behind the replacing character.

  - U+00A0 'Non breaking space'
    - You expect to see : (nothing or a space)
    - You see           : Â
      - replaced by     : Replaced by a single space (' '). Mind that this effort is about
                          data - not the mark-up presenting the data : the content itself.
                          In this case it only makes sense to replace it with a space-character.
                          After data is pulled from a file / database / stream / etc. it is the
                          front-end developer who needs to concern himself with presenting it.

  - U+00AD 'Soft hyphen'
    - You expect to see : (nothing or a space)
    - You see           : Â
      - replaced by     : Replaced by a single space (' ').
                          A soft hyphen (ISO 8859: 0xAD, HTML: &#173; &shy;) or syllable hyphen
                          (EBCDIC: 0xCA), abbreviated SHY, is a code point reserved in some coded
                          character sets for the purpose of breaking words across lines by inserting
                          visible hyphens. Based on my personal encounters with this beast, it
                          made most sense replacing it with a (' '), since this process is all about
                          the data : not about presenting it.

The array is maintained in order of the character's Windows 1252 hex-code, for
the sole simple reason that ascii tables (which I used to look up information)
always are.

Written in PHP since that gives me flexibility to deploy it in a quick-n-dirty
shell script to prepare and parse existing data before loading it into a db,
as well in an online service where an existing mangled data stream needs fixing
before further processing / storage in a database (UTF-8).

#### Again : this should NOT be your solution - this is for fixing 'what-already-is'.

#### Author: OldskoolOrion (H.Coenen)
