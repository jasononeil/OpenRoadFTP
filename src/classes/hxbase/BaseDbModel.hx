package hxbase;
import hxbase.DbControl;

/** 
<b>Conventions</b>
<br />This model follows a few simple conventions
<ul>
	<li>The database details will already be defined in AppConfig.</li>
	<li>The database table will have the same name as this class.</li>
	<li>The primary id will be a column called 'id'.</li>
</ul>
Follow these and you'll be off to a good start.

<b>Various Variable Names</b>
<ul>
	<li>When you extend this class, you'll need to define a public 
	variable for each column in your table.</li>
	<li>Basic definitions are public and non-static, like:
	<br />public var name : String;</li>
	<li>How you know which type to use:
		<ul>
			<li>Int: TINYINT SHORT LONG INT24</li>
			<li>Float: LONGLONG, DECIMAL, FLOAT, DOUBLE</li>
			<li>Bool: TINYINT(1)</li>
			<li>Date: DATE, DATETIME</li>
			<li>String: <All Others> (note - BLOB can contain \0)</li>
		</ul>
	</li>
</ul>

<b>The Manager</b>
<ul>
	<li>You use the manager to perform searches and SELECT statements, basically</li>
	<li>You'll have to add this line right before the end of your class definition:
	<br />public static var manager = new php.db.Manager< MyModelName >(MyModelName);</li>
</ul>

<b>Relationships</b>
<ul>
	<li><b>One to one</b> or <b>Belongs to</b>.
	<br />These relationships are built in by haxe.  You'll 
	need to include something like this:
	<pre>static function RELATIONS() 
{
	return &#91{ prop : "user", key : "userId", manager : User.manager }];
}</pre>

	Basically, you need a static function, which returns 
	an array with the details of each relationships.  The fields are:
	<ul>
		<li>prop: The name of the property in this object 
		that you want to store the related object in.</li>
		<li>key: The name of the property in this object
		that has the key for the related object.</li>
		<li>manager: The link to the manager for the Class 
		of the related object.</li>
	</ul>
	
	To insert multiple, do:
	<pre>static function RELATIONS() 
{
	return &#91{ prop : "profile", key : "profileId", manager : Profile.manager },
	{ prop : "mother", key : "motherId", manager : User.manager },
	{ prop : "father", key : "fatherId", manager : User.manager }];
}</pre>

... or something to that effect.</li>
	<li><b>Has Many</b>
	<br />This sort of relationship doesn't come built in in haxe,
	but wasn't very hard to get going.  I put in a bit of code like
	this:
	<pre>	public var todoList(getter_todoList,null):List< TodoItem >;
	private function getter_todoList():List< TodoItem > 
	{
		return (id == null) ? 
			new List<TodoItem>() : 
			TodoItem.manager.search({ userId : id }); 
	}</pre>
	Just replace "todoList" with whatever you want to call
	your list of child objects, and "TodoItem" with the 
	Class name for the model of your child objects.
	</li>
	<li><b>Many To Many</b>
	<br />This isn't as common, sometimes seen in tags.  It's a many-to-many, 
	and usually needs a joining table.  I haven't gone and done these yet...
	<br />I imagine you could do it by having a model for the joining table
	and just doing a hasMany relationship from Model1 -> ModelJoin and from
	ModelJoin -> Model2.  The SimpleSQL book I was reading seemed to suggest
	this, with the model for the Join table containing a hasMany relationship
	to both other models.</li>
	
</ul>

<b>Automating this</b>

Boring!  Typing in all this stuff could probably be automated 
to be read directly from the database...

<b>Validation</b>

I'm still going to have to think about this

<b>General Usage</b>

Have a look at <a href="http://haxe.org/doc/neko/spod">the haxe SPOD tutorial</a>,
because that's what all this is based on.
*/
class BaseDbModel extends php.db.Object
{
	
}
