Create action files which must start with the following header:
<?php
/*
Action Title: Stub
Action Description: Does Nothing
Action Events: *
Action Objects: *
Action Filter:
*/
?>
Action Title		is what the user sees when assigning actions to various events.
Action Description	a short description (can handle line breaks) describing the action.
Action Events		use * to match any event, otherwise specify a list of comma separated events.
Action Objects		use * to match any context object, otherwise specify a list of comma separated class-names.
Action Filter		while each event is triggered globally, some actions must meet specific requirements (special users or so)

Available for the code is a context variable on which the action acts. The context is a class indexed array of various objects.
