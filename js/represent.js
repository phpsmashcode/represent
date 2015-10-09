function callback__search_api_result_click(value, id)
{
	document.getElementById("txt__rcc_question").value = value;
	document.getElementById("txt__rcc_question").title = id;
	document.getElementById("rcc_search_result").innerHTML = '';
}