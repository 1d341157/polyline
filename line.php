<?php  

//MIME types for HTTP  
define( 'HTTP_CONTENT_TYPE_SVG', 'image/svg+xml' );  
define( 'HTTP_CONTENT_TYPE_HTML', 'text/html' );  

//Used to define the SVG document's extremes  
define( 'SVG_WIDTH', 800 );  
define( 'SVG_HEIGHT', 600 );  

//Used to define the polylines' (polygonal chain's) limits  
define( 'POLYLINE_VERTICES', rand( 4, 7 ) );  
define( 'POLYLINES', 1 );  

/**  
 * A point in three dimensional Euclidean space  
 */  
class point  
{  
  //Each point must have an x, y, and z coordinate; and may or may not have a name  
  private $x = 0, $y = 0, $z = 0;  
  private $name = '';  

  //All of the parameters used for creating a point are optional  
  public function __construct( $x = 0, $y = 0, $z = 0, $name = '' )  
  {  
    //Define this x, y, and z coordinates for this point  
    $this->x = $x;  
    $this->y = $y;  
    $this->z = $z;  

    //Define the name of this point  
    $this->name = $name;  
  }  

  //Govern evaluation of private properties  
  public function __get( $property )  
  {  
    //Default return value  
    $value = null;  

    //Define value based upon name of property requested  
    switch( $property )  
    {  
      case 'x':  
      case 'y':  
      case 'z':  
      case 'name':  
        $value = $this->$property;  
        break;  
    }  

    //Return a property's value or null  
    return( $value );  
  }  

  //Return a comma separated string of x, y, and z coordinates  
  public function __toString( )  
  {  
    //Concatenate the string from the object's properties  
    $string = '(' . $this->x . ',' . $this->y . ',' . $this->z . ')';  

    return( $string );  
  }  
}  

/**  
 * Takes two instances of point to construct  
 */  
class line_segment  
{  
  //The line segment has two points and may also have a name  
  private $p1, $p2;  
  private $name = '';  

  //Creating a line segment requires two points and it may optionally have a name  
  public function __construct( $p1, $p2, $name = '' )  
  {  
    //Test whether the two points are in fact points  
    if( $p1 instanceof point && $p2 instanceof point )  
    {  
      //Define the two end points of this line segment  
      $this->p1 = $p1;  
      $this->p2 = $p2;  

      //Define the name of this line segment  
      $this->name = $name;  
    }  
  }  

  //Govern evaluation of private properties  
  public function __get( $property )  
  {  
    //Default return value  
    $value = null;  

    //Define value based upon name of property requested  
    switch( $property )  
    {  
      case 'p1':  
      case 'p2':  
      case 'name':  
        $value = $this->$property;  
        break;  
    }  

    //Return a property's value or null  
    return( $value );  
  }  
}  

/**  
 * Takes an array of points as input and define's them as the polygonal chain's vertices  
 */  
class polygonal_chain  
{  
  //A protected place to store the vertices  
  protected $vertices = array( );  

  //The polygonal chain may have a name  
  private $name = '';  

  public function __construct( $name = 'A' )  
  {  
    //Define the name of this point  
    $this->name = $name;  

    //Validate that we have an array of points  
    if( is_array( $points ) && count( $points ) )  
    {  
      //The array of points become the vertices of the polygonal chain  
      $this->consume( $points );  
    }  
  }  

  //Assign an array of points to the object's array of vertices  
  protected function consume( $points )  
  {  
    //Loop through points array  
    foreach( $points as $vertex )  
    {  
      //Check that they are in fact instances of point  
      if( $vertex instanceof point )  
      {  
        //Add the accepted vertex to the object's array of vertices  
        $this->vertices[ ] = $vertex;  
      }  
    }  
  }  

  //Govern evaluation of private properties  
  public function __get( $property )  
  {  
    //Default return value  
    $value = null;  

    //Define value based upon name of property requested  
    switch( $property )  
    {  
      case 'name':  
        $value = $this->$property;  
        break;  
    }  

    //Return a property's value or null  
    return( $value );  
  } 

  //Return a string of coordinates  
  public function __toString( )  
  {  
    //Take the array of vertices and create a comma separated list of them; calling each of their __toString functions  
    $string = implode( ',', $this->vertices );  

    return( $string );  
  } 
}  

/**  
 * Extends polygonal_chain and changes the constructor to randomly generate the points using an algorithm  
 */  
class polygonal_chain_random extends polygonal_chain  
{  
  //Takes an integer as input value, which determines how mant vertices the polygonal chain shall have  
  public function __construct( $limit ) 
  {  
    //Convert the parameter to an integer  
    $limit = intval( $limit );  

    //Generate some points; -it isn't a polygonal chain without them!  
    $points = $this->seed( $limit );  

    //The array of points become the vertices of the polygonal chain  
    $this->consume( $points );  
  }  

  //Generate random points for the polygonal chain  
  protected function seed( $limit, $points = array( ) )  
  {  
    //Keep generating points while the value of $limit is a truthy value  
    if( $limit-- )  
    {  
      //Create arbitrary x and y coordinates  
      $x = rand( 0, SVG_WIDTH );  
      $y = rand( 0, SVG_HEIGHT );  

      //Create a new point using the above coordinates  
      $points[ ] = new svg_point( $x, $y );  

      //Proceed to the next iteration  
      $points = $this->seed( $limit, $points );  
    }  

    //Return the array of points  
    return( $points );  
  }  
}  

/**  
 * SVG Rendering; point __toString function to output coordinates in format compatible with SVG  
 */  
class svg_point extends point  
{  
  //Return a comma separated string of x, y, and z coordinates  
  public function __toString( )  
  {  
    //Concatenate the string from the object's properties  
    $string = $this->x . ' ' . $this->y;  

     return( $string );  
  }   
}  

/**  
 * Creates an SVG document using DOMDocument;  
 *  
 * @todo: add cartesian coordinate system  
 * @todo: convert to svg element and not document  
 */  
class svg_document  
{  
  //A private place to hold the SVG element's DOMDocument and the DOMDocument's root element  
  private $DOMDocument, $root_element;  

  //The SVG element should have a user defined width, height, and unit of measurement  
  private $width, $height, $unit = 'px', $xmlns = 'http://www.w3.org/2000/svg';  

  public function __construct( $width, $height, $unit = 'px' )  
  {  
    //Create an instance of DOMDocument and assign it to the property of the same name  
    $DOMDocument = new DOMDocument( );  
    $this->DOMDocument = $DOMDocument;  

    //Define the width, height, viewBox, and unit   
    $this->width = $width;  
    $this->height = $height;  
    $this->unit = $unit;  

    //Create the root document element for the SVG document  
    $this->root_element( );  
  }  

  //Create the document's root element; the SVG element  
  private function root_element( )  
  {  
    //Create the root SVG element  
    $svg = $this->DOMDocument->createElement( 'svg' );  

    //Add the width, height, and viewport atributes  
    $svg->appendChild( $this->DOMAttribute( 'width', $this->width . $this->unit ) );  
    $svg->appendChild( $this->DOMAttribute( 'height', $this->height . $this->unit ) );  
    $svg->appendChild( $this->DOMAttribute( 'viewBox', '0 0 ' . $this->width . ' ' . $this->height ) );  
    $svg->appendChild( $this->DOMAttribute( 'xmlns', $this->xmlns ) );  

    //Append the SVG element to the document  
    $this->DOMDocument->appendChild( $svg );  
    $this->root_element = $svg;  
  }  

  //Returns a domAttribute with the name and value  
  private function DOMAttribute( $name, $value )  
  {  
    //Create the attribute then assign the value  
    $attribute = $this->DOMDocument->createAttribute( $name );  
    $attribute->value = $value;  

    return( $attribute );  
  }  

  //Takes an array of attributes and adds them to a DOMElement; by reference  
  private function DOMAttributes( &$element, $attributes )  
  {  
    //Validate the DOMElement is negotiable  
    if( $element instanceof DOMElement )  
    {  
      //Check if the attributes are an array before trying to loop through it  
      if( is_array( $attributes ) )  
      {  
        //Loop through each attribute and add it to the element by reference  
        foreach( $attributes as $name => $value )  
        {  
          $element->appendChild( $this->DOMAttribute( $name, $value ) );  
        }  
      }  
    }  
  }  

  //Adds a list of styles via a STYLE element and an array of styles  
  public function stylesheet( $styles = array( ) )  
  {  
    //Validate the styles are in an array before trying to add them  
    if( is_array( $styles ) )  
    {  
      //Create the style element  
      $stylesheet = $this->DOMDocument->createElement( 'style', implode( PHP_EOL, $styles ) );  

      //Add the element's attributes  
      $stylesheet->appendChild( $this->DOMAttribute( 'type', 'text/css' ) );  

      //Append the style element to the document  
      $this->root_element->appendChild( $stylesheet );  
    }  
  }  

  //Add a polyline element to the document  
  public function polyline( $points, $attributes = array( ) )  
  {  
    //Create the polyline element  
    $polyline = $this->DOMDocument->createElement( 'polyline' );  

    //Add the element's attributes  
    $polyline->appendChild( $this->DOMAttribute( 'points', $points ) );  

    //Add the optional DOMElement attributes to the element by reference  
    $this->DOMAttributes( $polyline, $attributes );  

    //Append the polyline element to the document  
    $this->root_element->appendChild( $polyline );  
  }  

  //Govern evaluation of private properties  
  public function __get( $property )  
  {  
    //Default return value  
    $value = null;  

    //Define value based upon name of property requested  
    switch( $property )  
    {  
      case 'width':  
      case 'height':  
        $value = $this->$property;  
        break;  
    }  

    //Return a property's value or null  
    return( $value );  
  }  

  //Represent the SVG document in string form  
  public function __toString( )  
  {   
    //Return the saved DOMDocument instance as XML  
    return( $this->DOMDocument->saveXML( ) );  
  }  
}  

//Start output buffering, so we can send the right HTTP header  
ob_start( );  

//Define and initialize a string to concatenate the output too  
$output = '';  

//Create a new SVG  
$svg = new svg_document( SVG_WIDTH, SVG_HEIGHT );  

//Create an array of styles  
$styles[ ] = 'polyline{ stroke: black; stroke-width: 2; fill: none; }';  

//Add the array of styles to the SVG element  
$svg->stylesheet( $styles );  

foreach( get_declared_classes( ) as $class )  
{  
  if( is_subclass_of( $class, 'polygonal_chain' ) )  
  {  
    $polyline = new $class( POLYLINE_VERTICES );  

    //Add a polyline to the document  
    $svg->polyline( $polyline, array( 'class' => $class ) );  
  }  
}  

//Convert the SVG document to a string; indirectly calls svg_document::__toString( )  
$output = "$svg";  

//Send the SVG HTTP header if there is negotiable output  
if( !empty( $output ) )  
{  
  //Output  
  header( 'Content-Type: ' . HTTP_CONTENT_TYPE_SVG );  

  //Send SVG document to the output butter  
  echo $output;  
}  
else  
{  
  //Send http header for the HTML error message  
  header( 'Content-Type: ' . HTTP_CONTENT_TYPE_HTML );  

  //Error message.  
  echo 'No SVG document available.';  
}  

//Send contents of output buffer  
ob_flush( ); 
