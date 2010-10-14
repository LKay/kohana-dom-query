# Using DOM Query

Create an object of DOM Query. 

		$dom_query = new Dom_Query;

With this you can simply get DOM elements using both XPath expressions or well know CSS selectors using for 
example by jQuery library. Those you can freely use are:

*	**E**
*	**E, F**
*	**.class**
*	**#id**
*	**E F**
*	**E > F**
*	**E + F**
*	**E[attribute]**
*	**E[attribute=value]**
*	**E[attribute~=value]**

And many more. The simple way to get elements is:

		$elements = $dom_query->query('.my_class');
		// This will get you all elements with CSS class .my_class
	
		// Now you can iterate all elements one by one
		foreach ($elements as $element) {
			echo $element->nodeName; // This will echo tag name of the element 
		}