
<div class="row message-wrapper">
    <div class="col-sm-3">
        <input type="text" class="form-control mb-2" id="search" placeholder="Search">
        <div class="wrapper-data">
            <ul class="list-group"></ul>   
        </div>
    </div>
    <div class="col-sm-9">
        <div class="card">
            <div class="card-header">
                <p><?php echo $recipientName[0]['UserDetail']['name'] ?></p>
                <input type="text" placeholder="Search" id="search-message-details">
            </div>
            <div class="card-body pr-0" style="background-color:#f4f9ff;">
                <div class="messages-wrapper">
                    <div class="data"></div>
                </div>
                <div class="message-field-wrapper">
                    <form id="sendForm" method="POST">
                        <input type="text" name="data[Message][content]">
                        <button class="btn btn-primary">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .message-wrapper {  
        overflow:auto;
        position: relative;
    }

    ul li {
        cursor: pointer;
    }
    ul li:hover{
        background:#ccc;
        opacity:0.9;
    }
    ul li img{
        width:50px!important;
        height: 50px!important;
        object-fit:cover;
        border-radius:50%;
        margin-right:1rem;
        background-color:#000;
    }
    ul li > div{
        display:flex;
        align-items:center;
    }

    ul li div > p.name{
        font-weight:bold;
        text-transform:uppercase;
        font-size:16px;
        margin:0;
    }

    ul li div > p{
        margin:0;
        font-size:14px;
    }

    ul li div p.date{
        font-size:10px;
        text-align:right;
    }

    ul li div.lastMsg{
        color:#777;
    }

    ul li div.lastMsg div{
        width: 100%;
    }

    ul li div.lastMsg p.name{
        color:#000;
    }

</style>

<style>
    .highlight {
        background-color:yellow;
        color: #000;
    }
    .message-wrapper .card-header{
        display:flex;
        justify-content:space-between;
        align-items:center;
    }
    .card-header p {
        font-size:30px;
        font-weight:bold;
        margin:0;
    }
    .card-header input{
        padding:0.5rem 2.5rem 0.5rem 0.5rem;
        border-radius:5px;
        border:none;
        outline:1px solid #ccc;
    }
    .wrapper{
        margin:auto;
        width:700px;
    }
    .messages-wrapper{
        height:58vh;
        margin:auto;
        padding:0rem;
        position: relative;
        overflow:auto;
        display: flex;
        flex-direction: column;
        justify-content: end;
    }
    .messages-wrapper .data{
        overflow:auto;
    }
    .messages-wrapper .message-wrap{
        width:70%;
        margin-bottom:1rem;
    }
    .message-wrapper .message-wrap p.time{
        font-size:10px!important;
        text-align:right;
    }
    .message-wrapper .message-wrap .message-content{
        display:flex;
        align-items:flex-end;
    }
    .message-wrapper .message-wrap .message-content p.name{
        font-size:16px;
        text-transform:uppercase;
        font-weight:bold;
        color:#000;
    }
    .message-wrapper .message-wrap .message-content p{
        font-size:14px;
        margin:0;
        color:#666;
    }
    .messages-wrapper .message-wrap.my-message {
        margin-left:auto;
    }
    .messages-wrapper .message-wrap.not-my-message {
        margin-right:auto;
    }
    .message-wrapper .message-wrap.my-message .message-content {
        justify-content:flex-end;
    }
    .message-wrapper .message-wrap.my-message .message-content > div {
        padding:0.5rem;
        width:fit-content;
        border-radius:10px;
    }
    .message-wrapper .message-wrap.not-my-message .message-content {
        justify-content:flex-start;
    }
    .message-wrapper .message-wrap .message-content > div {
        color:#000;
        width:fit-content;
    }
    .message-wrapper .message-wrap.not-my-message .message-content .messages{
        padding:0.5rem 2rem;
        background:#ddd;
        border-radius:5px;
    }
    .message-wrapper .message-wrap.my-message .message-content .messages{
        padding:0.5rem 2rem;
        background:#ccf0ff;
        border-radius:5px;
    }
    .messages-wrapper .message-wrap img{
        max-width:50px!important;
        width:100%;
        height:50px!important;
        border-radius:50%;
        margin-right:1rem;
        object-fit:cover;
        background-color:#000;
        position: relative;
        margin-bottom:25px;
    }
    .message-field-wrapper{
        position: relative;
    }
    .message-field-wrapper input{
        width:79%;
        box-sizing:border-box;
        height:60px;
        padding-left:1rem;
    }
    .message-field-wrapper button{
        width:20%;
        box-sizing:border-box;
        height:63px;
        margin:0;
    }
    .messages-wrapper .message-wrap a{
        display: none;
    }
</style>

<script>
    $(document).ready(function(){

        displayUsers();
        loadUSerMessages(<?php echo $id; ?>);

        var intervalIDs = [];


        runChatMessage(intervalIDs);

        $(document).on('click', '.message-wrapper li', function(){
            $('#messageUserModal .modal-title').text($(this).find('p.name').text()).css('text-transform','uppercase');
            $('#messageUserModal .modal-footer button.view').attr('data-url',$(this).attr('data-url'));
            $('#messageUserModal .modal-footer button.delete').attr('data-id',$(this).attr('data-id'));
            $('#messageUserModal').modal({
                keyboard: false,
                backdrop:'static'
            });
        });

        $('#search').on('keyup', function(){
            var value =  $(this).val();
            displayUsers('search', value);
        }); 
        
        $(document).on('click','#show-more', function(e){
            e.preventDefault();
            const page = $(this).data('current');

            const limit =10;

            if($(this).hasClass('back')) {
                selectedPage = page - limit;
            } else {
                selectedPage = page + limit;
            }

            displayUsers('limit', selectedPage);
        });
        
        function displayUsers(option,value) {
            var data = {};
            if (option === "search") {
                data = { keywords:value};
            } 
            if (option === "page") {
                data = { page:value }
            } 
            if (option === "limit") {
                data = {limit:value};
            }


            $.ajax({
                url: "<?php echo $this->Html->url(array('controller'=>'messages','action' => 'list'))?>",
                type:'GET',
                dataType:'json',
                contentType: 'application/json',
                data: data,
                success:function(res){
                    $('.message-wrapper .wrapper-data').html(res.html);
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                }
            });
        }

        $('.messages-wrapper').scrollTop($('.messages-wrapper')[0].scrollHeight);
        var countArr = $('.messages-wrapper .message-wrap').length;

        function newData() {
            loadUSerMessages(<?php echo $id; ?>);
            // var count = $('.messages-wrapper .message-wrap').length;
            // if (countArr != count) {
            //     countArr = count;
            //     $('.messages-wrapper').animate({
            //         scrollTop:$('.messages-wrapper')[0].scrollHeight
            //     }, "slow");
            // }
            
        }

        $(document).on("click mouseover", ".messages-wrapper", function(){
           clearInterval(intervalIDs[0]);
        });

        function chatContainer() {
            $('.messages-wrapper').load(location.href + ' .messages-wrapper>*', "");
        }

        function loadUSerMessages(id,search) {
            $.ajax({
                url:'<?php echo $this->Html->url(array('controller'=>'messages','action' => 'viewUserMessage')) ?>',
                data:{id:id, search:search},
                dataType:'json',
                success:function(response){
                    $('.messages-wrapper .data').replaceWith(response.html);
                    $('.messages-wrapper').animate({
                        scrollTop:$('.messages-wrapper')[0].scrollHeight
                    }, "slow");
                },
                error:function(error){
                    alert(error);
                }
            });
        }

        $(document).on('click', '.message-wrapper .wrapper-data ul li', function(){
            window.location.href = $(this).attr('data-url');
        });

        $('#search-message-details').on('keyup', function(e){
            var searchText = $(this).val().toLowerCase();
            loadUSerMessages(<?php echo $id; ?>,searchText);
            runChatMessage('stop');
            setTimeout(() => {
                $('.messages-wrapper .message-wrap p.details').each(function() {
                    var rowText = $(this).text().toLowerCase();
                    if (rowText.includes(searchText)) {
                        $(this).html(rowText.replace(new RegExp(searchText, 'gi'), '<span class="highlight">$&</span>'));
                    } else {
                        $(this).html(rowText);
                    }
                });
            }, 100);
        });

        $('#sendForm').submit(function(e){
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                data: formData,
                success: function(response) {
                    loadUSerMessages(<?php echo $id; ?>);
                    $('#sendForm').trigger("reset");
                    displayUsers();
                    $('.messages-wrapper').animate({
                    scrollTop:$('.messages-wrapper')[0].scrollHeight
                }, "slow");
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });   

        
        function runChatMessage(intervalIDs) {
            var reFresh = setInterval(function() {
                newData();
                    displayUsers();
            }, 3000);
            intervalIDs.push(reFresh);
        }
       
    });

    
</script>
