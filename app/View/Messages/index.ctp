<div class="container message-wrapper">
    <a href="<?php echo $this->Html->url(array('action' => 'send'))?>">NEW MESSAGE</a>
    <input type="text" class="form-control mb-2" id="search" placeholder="Search">
    <div class="wrapper-data">
        <ul class="list-group"></ul>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="messageUserModal" tabindex="-1" role="dialog" aria-labelledby="messageUserModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageUserModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger delete">Delete Message</button>
                <button type="button" class="btn btn-primary view">View Message</button>
            </div>
        </div>
    </div>
</div>

<style>
.message-wrapper {
    overflow: auto;
    position: relative;
}

ul li {
    cursor: pointer;
}

ul li:hover {
    background: #ccc;
    opacity: 0.9;
}

ul li img {
    width: 50px !important;
    height: 40px !important;
    object-fit: contain;
    border-radius: 50%;
    margin-right: 1rem;
    background-color: #000;
}

ul li>div {
    display: flex;
    align-items: center;
}

ul li div>p.name {
    font-weight: bold;
    text-transform: uppercase;
    font-size: 16px;
    margin: 0;
}

ul li div>p {
    margin: 0;
    font-size: 14px;
}

ul li div p.date {
    font-size: 10px;
    text-align: right;
}

ul li div.lastMsg {
    color: #777;
}

ul li div.lastMsg div {
    width: 100%;
}

ul li div.lastMsg p.name {
    color: #000;
}
</style>

<script>
$(document).ready(function() {

    displayUsers();

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

    $(document).on('click', '#messageUserModal .modal-footer button.view', function(e) {
        e.preventDefault();
        window.location.href = $(this).data('url');
    });

    $(document).on('click', '#messageUserModal .modal-footer button.delete', function(e) {
        e.preventDefault();
        const id = $(this).attr('data-id');
        if (confirm('Are you sure your want to delete?')) {
            $.ajax({
                url: '<?php echo $this->Html->url(array('controller' => 'messages', 'action' => 'deleteUserMessage')) ?>',
                type: "GET",
                dataType: 'json',
                contentType: 'application/json',
                data: {
                    id: id
                },
                success: function(response) {
                    console.log(response);
                    if (response.html === "success") {
                        $('ul li[data-id="' + id + '"').remove();
                        $('#messageUserModal').modal('hide');
                        displayUsers();
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

    });

    $('#search').on('keyup', function() {
        var value = $(this).val();
        displayUsers('search', value);
    });

    $(document).on('click', '#show-more', function(e) {
        e.preventDefault();
        const page = $(this).data('current');

        const limit = 2;

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

});
</script>