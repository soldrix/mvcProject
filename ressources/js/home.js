$('#btnForm').on('click',function () {
    $.ajax({
        type: "post",
        url: "/api/testPost",
        headers:{
            "Accept" : "application/json"
        },
        data : {
            "email" : $('#email').val()
        },
        success:function (data) {
            console.log(data);
        },error:function (error) {
            console.log(error)
        }
    })
})