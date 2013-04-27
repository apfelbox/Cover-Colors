# Cover Colors
This is an example app, trying to mimic the iTunes cover colors algorithm.
A live example is visible [online](http://streuobstwie.se/cover_colors/).

## How it works
It is a more or less direct port from Mathematica of the example code, provided by [Seth Thompson][1].

### Preparation of the image
* At first, the image is scaled down to max-width of 36px and max-height of 36 (to reduce the computation cost)
* A border of 1px around the image is removed, so that the new max. dimensions are 34x34.

The removal of the border is important, because some of the images have a fringy border (or just a normal border, too):

![Amy MacDonald](http://streuobstwie.se/cover_colors/readme/amy_macdonald.jpg)

Found colors: <span style="background-color: #897e61; padding: 2px;">#897e61</span>, <span style="background-color: #0a0705; color: white; padding: 2px;">#0a0705</span>, <span style="background-color: #d7b67b; padding: 2px;">#d7b67b</span>

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

Found colors: <span style="background-color: #e7c985; padding: 2px;">#e7c985</span>, <span style="background-color: #643d2a; color: white; padding: 2px;">#643d2a</span>, <span style="background-color: #906e4e; color: white; padding: 2px;">#906e4e</span>

### Looking for a better contrast between background color and text colors
Currently, the text colors are chosen, so that there is a large enough (to be defined) color difference between the colors.
This can lead to potentialy low contrast.


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