<?php echo $this->Html->css('message'); ?>
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
                    <div id="new-message">NEW MESSAGE <span>CLICK TO RELOAD</span></div>
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

<script>
$(document).ready(function() {

    displayUsers();
    loadUSerMessages(<?php echo $id; ?>);

    var totalMessage = [];

    $(document).on('click', '.message-wrapper li', function() {
        $('#messageUserModal .modal-title').text($(this).find('p.name').text()).css('text-transform',
            'uppercase');
        $('#messageUserModal .modal-footer button.view').attr('data-url', $(this).attr('data-url'));
        $('#messageUserModal .modal-footer button.delete').attr('data-id', $(this).attr('data-id'));
        $('#messageUserModal').modal({
            keyboard: false,
            backdrop: 'static'
        });
    });

    setTimeout(function() {
        totalMessage.push($('.messages-wrapper .data').attr('data-total'));
        setInterval(() => {
            checkMessage();
        }, 3000);
    }, 1000);

    function checkMessage() {
        $.ajax({
            url: '<?php echo $this->Html->url(array('controller'=>'messages','action' => 'countUserMessage')) ?>',
            data: {
                id: '<?php echo $id; ?>',
            },
            dataType: 'json',
            success: function(response) {
                var newTotal = response.total;
                var oldTotal = totalMessage[0];
                console.log(newTotal + '=' + oldTotal);
                if (newTotal != oldTotal) {
                    $('div#new-message').addClass('open');

                }
            },
            error: function(error) {
                alert(error);
            }
        });


    }

    $('#search').on('keyup', function() {
        var value = $(this).val();
        displayUsers('search', value);
    });

    $(document).on('click', '#show-more', function(e) {
        e.preventDefault();
        const page = $(this).data('current');

        const limit = 10;

        if ($(this).hasClass('back')) {
            selectedPage = page - limit;
        } else {
            selectedPage = page + limit;
        }

        displayUsers('limit', selectedPage);
    });

    function displayUsers(option, value) {
        var data = {};
        if (option === "search") {
            data = {
                keywords: value
            };
        }
        if (option === "page") {
            data = {
                page: value
            }
        }
        if (option === "limit") {
            data = {
                limit: value
            };
        }


        $.ajax({
            url: "<?php echo $this->Html->url(array('controller'=>'messages','action' => 'list'))?>",
            type: 'GET',
            dataType: 'json',
            contentType: 'application/json',
            data: data,
            success: function(res) {
                $('.message-wrapper .wrapper-data').html(res.html);
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error(error);
            }
        });
    }

    function loadUSerMessages(id, search, page, limit) {
        $.ajax({
            url: '<?php echo $this->Html->url(array('controller'=>'messages','action' => 'viewUserMessage')) ?>',
            data: {
                id: id,
                search: search,
                page: page,
            },
            dataType: 'json',
            success: function(response) {

                $('.messages-wrapper .data').replaceWith(response.html);

                if (response.error) {
                    toastr['error'](response.error);
                    window.location.href =
                        "<?php echo $this->Html->url(array('controller'=>'messages','action'=>'index')) ?>";
                }

                $('.messages-wrapper .data').animate({
                    scrollTop: $('.messages-wrapper .data')[0].scrollHeight
                }, "slow");

            },
            error: function(error) {
                alert(error);
            }
        });
    }

    $(document).on('click', '.message-wrapper .wrapper-data ul li', function() {
        window.location.href = $(this).attr('data-url');
    });

    $(document).on('click', 'p#msg-details-showmore', function(e) {
        e.preventDefault();
        const page = $(this).attr('data-current');
        selectedPage = (parseInt(page) + 1);
        $(this).remove();
        loadUSerMessages(<?php echo $id; ?>, '', selectedPage);
    });

    $(document).on('click', 'p#msg-details-back', function() {
        const page = $(this).attr('data-current');
        selectedPage = (parseInt(page) - 1);
        $(this).remove();
        loadUSerMessages(<?php echo $id; ?>, '', selectedPage);
    });

    $('#search-message-details').on('keyup', function(e) {
        var searchText = $(this).val().toLowerCase();
        loadUSerMessages(<?php echo $id; ?>, searchText);
        $('.messages-wrapper').animate({
            scrollTop: $('.messages-wrapper')[0].scrollHeight
        }, "slow");
        setTimeout(() => {
            $('.messages-wrapper .message-wrap p.details').each(function() {
                var rowText = $(this).text().toLowerCase();
                if (rowText.includes(searchText)) {
                    $(this).html(rowText.replace(new RegExp(searchText, 'gi'),
                        '<span class="highlight">$&</span>'));
                } else {
                    $(this).html(rowText);
                }
            });
        }, 100);
    });

    $(document).on('click', 'div#new-message', function() {
        loadUSerMessages(<?php echo $id; ?>);
        $(this).removeClass('open');
        var oldTotal = totalMessage[0];
        const newTotal = parseInt(oldTotal) + 1;
        totalMessage = [newTotal];
    });

    $('#sendForm').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.error) {
                    toastr['error'](response.error);
                    return false;
                }

                var oldTotal = totalMessage[0];
                const newTotal = parseInt(oldTotal) + 1;
                totalMessage = [newTotal];

                $('#sendForm').trigger("reset");
                setTimeout(() => {
                    $('.messages-wrapper .data').html('');
                    loadUSerMessages(<?php echo $id; ?>);
                    displayUsers();
                    $('.messages-wrapper .data').animate({
                        scrollTop: $('.messages-wrapper .data')[0]
                            .scrollHeight
                    }, "slow");
                }, 100);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });


});
</script>