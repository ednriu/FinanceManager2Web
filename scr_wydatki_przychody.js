//Script fills the data input with today date as default

function TodayDate()
{
	var date = '';
	var today = new Date();

	date = today.getFullYear();

	if(today.getMonth().toString().length==1)
	{
		date = date + '-'+ '0'+(today.getMonth()+1);
	};
	if(today.getMonth().toString().length==2)
	{
		date = date + '-'+(today.getMonth()+1);
	};

	if(today.getDate().toString().length==1)
	{
		date = date + '-0'+today.getDate();
	};
	if(today.getDate().toString().length==2)
	{
		date = date + '-' +today.getDate();
	};
	document.getElementById('datePicker').value = date;
};

function test()
{
	alert("test Alert");
};
//click on the radio which disables other options
function handleClick(myRadio) {
	if(myRadio.value== "true")
		{
		document.getElementById("datep1").disabled = true;
		document.getElementById("datep2").disabled = true;
		}
	else
		{
		document.getElementById("datep1").disabled = false;
		document.getElementById("datep2").disabled = false;
		}
};
//click on the radio which enables other options
function handleClick2(myRadio) {
	if(myRadio.value== "true")
		{
		document.getElementById("datep1").disabled = false;
		document.getElementById("datep2").disabled = false;
		}
	else
		{
		document.getElementById("datep1").disabled = true;
		document.getElementById("datep2").disabled = true;
		}
};
//showing and hidding password
function showHidePassword() {
  var x = document.getElementById("haslo");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
