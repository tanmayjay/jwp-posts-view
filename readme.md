# Posts View
It's a ***Wordpress Plugin*** that includes a `shortcode` for frontend functionality.

## Shortcode
### `[jpv-sc]`
### Functionality
Shows 10 (by default) latest *post titles* with their *view count*. There is a form given where user can choose the number of posts, category of posts, and whether the post will be ordered by their view counts in ascending or descending order.

An authorized user can also choose all these as well as the IDs of posts of which only the *excerpt* will be shown by passing parameter through the ***shortcode***.
### Parameters
- `numposts` *[int]* : Determines the number of posts to be shown.
- `category` *[string]* : Determines the category of posts to be shown. The values should be comma (,) seperated for multiple values and no inverted comma ('') is needed.
- `order` *[string]* : Determines the category of posts to be shown. If this parameter is given, the posts will be ordered in the given order by the total views. Only *ASC* or *DESC* is expected to be the value.
- `ids` *[int]* : Determines the ids of the posts of which excerpts will be shown. If this parameter is not given, excerpt will not be shown for any post. The values should be comma (,) seperated for multiple values and no inverted comma is needed.

### Example

    [jpv-sc numposts=20 category=blog,tutorial ids=1,2,3 order=DESC]
