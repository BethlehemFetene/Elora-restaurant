function orderMessage(){
    alert("Order Submitted Successfully");
}

function validateReservation(){

    let name = document.getElementById("name").value;

    if(name == ""){
        alert("Name is required");
        return false;
    }

    return true;
}