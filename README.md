# Cover Colors
This is an example app, trying to mimic the iTunes cover colors algorithm.

## How it works
It is a more or less direct port from Mathematica of the example code, provided by [Seth Thompson][1].

### Preparation of the image
* At first, the image is scaled down to max-width of 36px and max-height of 36 (to reduce the computation cost)
* A border of 1px around the image is removed, so that the new max. dimensions are 34x34.

The removal of the border is important, because some of the images have a fringy border (or just a normal border, too):

![Amy MacDonald](http://streuobstwie.se/cover_colors/readme/amy_macdonald.jpg)

Found colors: ![#897e61](http://streuobstwie.se/cover_colors/readme/colors/897e61.jpg), ![#0a0705](http://streuobstwie.se/cover_colors/readme/colors/0a0705.jpg), ![#d7b67b](http://streuobstwie.se/cover_colors/readme/colors/d7b67b.jpg)

### Background Color
* A histogram of the 1px wide edge of the image is created and the most dominant color is selected


### "Title" & "Song" Colors
* A histogram of the complete image is created, which groups all (in the YUV color space) similar colors into buckets
* These buckets are sorted downwars.
* Now, from the top down two colors are searched, which are different enough from the background color
* If no two colors are found, then the background color is analyzed, whether it is a dark color (`luminance < 0.5`) and white is used (black otherwise).


## Possible further optimizations
This algorithm (and implementation!) is not optimal in many cases:

### Simple AI additions to filter out highlights
Example: the bow tie of Aloe Blacc is not recognized:

![Aloe Blacc - Good Things](http://streuobstwie.se/cover_colors/readme/aloe_blacc.jpg)

Found colors: ![#e7c985](http://streuobstwie.se/cover_colors/readme/colors/e7c985.jpg), ![#643d2a](http://streuobstwie.se/cover_colors/readme/colors/643d2a.jpg), ![#906e4e](http://streuobstwie.se/cover_colors/readme/colors/906e4e.jpg)

### Looking for a better contrast between background color and text colors
Currently, the text colors are chosen, so that there is a large enough (to be defined) color difference between the colors.
This can lead to potentialy low contrast, especially if you use the colors without any separation.

Example: Adele - 21

![Adele - 21 in example app](http://streuobstwie.se/cover_colors/readme/adele_screenshot.png)

Found colors: ![#4f504a](http://streuobstwie.se/cover_colors/readme/colors/4f504a.jpg), ![#babbbd](http://streuobstwie.se/cover_colors/readme/colors/babbbd.jpg), ![#97989a](http://streuobstwie.se/cover_colors/readme/colors/97989a.jpg)


### This specific implementation
This implementation was created in just 3 hours, so there might be bugs or places for optimizations included. :-)


# Sources & additional variations
* [Original by Seth Thompson][1]
* [One of the many implementations of Seth Thompson's algorithm in JavaScript](https://github.com/lukashed/itunes-colors)
* [The YUV color space details (esp. translation RGB -> YUV)](http://en.wikipedia.org/wiki/YUV)
* [An implementation in Objective-C with some differences](http://www.panic.com/blog/2012/12/itunes-11-and-colors/)
* [An other approach using R-Trees](http://99designs.com/tech-blog/blog/2012/08/02/color-explorer/)
* [Yet another approach using k-means clustering](http://charlesleifer.com/blog/using-python-and-k-means-to-find-the-dominant-colors-in-images/)

The rights of all used cover images lie with the appropriate copyright owners.

[1]: https://github.com/s3ththompson/iTunes-11-Color-Algorithm
