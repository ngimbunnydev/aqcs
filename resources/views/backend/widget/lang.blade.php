@php

	$langs= config('ccms.bankendlang');
	$sess_lang=Session::get('lang');
	if(isset($sess_lang))
	{
		unset($langs[$sess_lang]);
	}
@endphp	
											
@foreach ($langs as $key)
	<li>
		<a href="?lang={{$key[0]}}">
			<i class="ace-icon fa fa-language"></i>
			{{__('ccms.'.$key[0])}}
		</a>
	</li>																										
@endforeach
