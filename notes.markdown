Notes and ideas
===============


Issues:
-------

* Subsystem to track updates to followers lists. Weighed by peerrank.
* What to do with connections outside community?

 
Weighting trust
---------------

A simple way of preferring two-way/mutual links over one way followers.

* `x` - number of followers
* `y` - number of friends
* `z` - total value passed
* `x + 1.5y` - number of virtual followers
* `z / (x + 1.5y)` - bonus per x
* `1.5x` - bonus per y 

### For example: ###

6 friends links and 3 followers. 1200 points to share

* `bonus = 1200 / ( 3 + (6 * 1.5) )`
* `bonus = 1200 / ( 3 + 9 )`
* `bonus = 1200 / 12`
* `bonus = 100`
* `100` - follower bonus
* `150` - friend bonus

