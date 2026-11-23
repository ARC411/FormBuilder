$(document).ready(function()
{
  updateTable()

});

function updateTable()
{
  const xhr = new XMLHttpRequest();
  xhr.open("GET", (window.BASE_URL || '/') + "history/form", true);
  xhr.onload = (e) => {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        var table = document.getElementById('table')
        $('#submit').show();
        table.innerHTML=xhr.responseText;
      } else {
        var table = document.getElementById('table')
        $('#submit').show();
        table.innerHTML="";
        console.error(xhr.statusText);
      }
    }
  };
  xhr.onerror = (e) => {
    console.error(xhr.statusText);
  };
  xhr.send(null);
}