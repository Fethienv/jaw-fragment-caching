jQuery((function ($) {

    var req_url = $("[id=request_url]").attr("data-req-url");
    var api_key = $("[id=request_url]").attr("data-api-key");
    var fragment_path = $("[id=request_url]").attr("data-fragment-path");
    var req_allow_direct_link = $("[id=request_url]").attr("data-allow-direct-link");
    var tbody = $('#list');
    var lds_spiner = $('#lds-spiner');
    var message_box = $('#jaw_message');
    let this_rows = [];

    $(document).ready(function () {
        open_dir(fragment_path);
        message_box.empty();
        message_box.append('<p>Ready!</p>');
        $('a[id=open_fragment_home]').on("click", function () {
            open_fragment_home();
        });
        $('a[id=cleanup_all_fragments]').on("click", function () {
            cleanup_all_fragments();
        });

        table_search();

    });

    function open_fragment_home() {
        open_dir(fragment_path);
        message_box.empty();
        message_box.append('<p>Ready!</p>');
    }

    function cleanup_all_fragments() {
        delete_file_or_dir(fragment_path, fragment_path,true);
        message_box.empty();
        message_box.append('<p>Ready!</p>');
    }

    function delete_file_or_dir(delete_this_path, open_path,re_create) {
        var r = confirm("Are you sure? did you want to delete " + delete_this_path);
        if (r == true) {
            tbody.empty();
            var data = {apikey: api_key,
                delete_path: delete_this_path,
                re_create_path: re_create,
                action: "delete"
            };
            var html = "";
            var file_list = send_data(req_url, data);
            file_list.done(function (recived_data) {
                $.each(recived_data.results, function (k, v) {
                    html += '<p>' + v.removed_path + ' ' + (v.result ? 'deleted' + ' re_create:' + recived_data.re_create : 'not deleted') + '</p>';
                });
                open_dir(open_path);
                message_box.empty();
                message_box.append(html);
            });
        } else {
            if (message_box.hasClass("jaw_error")) {
                message_box.removeClass("jaw_error");
            }
            if (message_box.hasClass("jaw_success")) {
                message_box.removeClass("jaw_success");
            }
            message_box.addClass("jaw_init");
            message_box.empty();
            message_box.append('<p>Delete action cancled</p>');
        }
    }

    function download_file(download_this_file) {
        var data = {apikey: api_key,
            download_path: download_this_file,
            action: "download"
        };
        var file_list = send_data(req_url, data);
        file_list.done(function (recived_data) {
            message_box.empty();
            message_box.append('<p>Dowloading is starting</p>');
        });
    }

    function upload_file(download_this_file) {
        var data = {apikey: api_key,
            download_path: download_this_file,
            action: "download"
        };
        var file_list = send_data(req_url, data);
        file_list.done(function (recived_data) {
            message_box.empty();
            message_box.append('<p>Dowloading is starting</p>');
        });
    }

    function create_folder(download_this_file) {
        var data = {apikey: api_key,
            download_path: download_this_file,
            action: "download"
        };
        var file_list = send_data(req_url, data);
        file_list.done(function (recived_data) {
            message_box.empty();
            message_box.append('<p>Dowloading of ' + recived_data.download_path + ' is startion</p>');
        });
    }

    function open_dir(open_this_path) {
        tbody.empty();
        var data = {apikey: api_key,
            open_path: open_this_path,
            fragment_dir: fragment_path,
            action: "list"
        };

        var file_list = send_data(req_url, data);
        file_list.done(function (recived_data) {
            $.each(recived_data.results, function (k, v) {
                tbody.append(renderFileRow(v));
            });
            !recived_data.results.length && tbody.append('<tr><td class="empty" colspan=5>This folder is empty</td></tr>');
            recived_data.is_writable ? $('body').removeClass('no_write') : $('body').addClass('no_write');

            renderBreadcrumbs(open_this_path);

            $('a[id^=open_fragment_dir_]').on("click", function () {
                var open_path = $(this).attr("data-open-path");
                open_dir(open_path);
                message_box.empty();
                message_box.append('<p>Ready!</p>');
            });
            $('a[id^=breadcrumb_dir_]').on("click", function () {
                var open_path = $(this).attr("data-open-path");
                open_dir(open_path);
                message_box.empty();
                message_box.append('<p>Ready!</p>');
            });
            $('a[id^=download_fragment_]').on("click", function () {
                var download_path = $(this).attr("data-download-path");
                download_file(download_path);
            });
            $('a[id^=delete_fragment_]').on("click", function () {
                var open_path = $(this).attr("data-parent-path");
                var delete_path = $(this).attr("data-delete-path");
                delete_file_or_dir(delete_path, open_path,false);
            });
            this_rows = table_pagination(false);
        }).fail(function () {
            tbody.empty();
            tbody.append('<tr><td class="empty" colspan=5>Please click Home in navbar to reload content</td></tr>');
        });
    }

    function table_pagination(this_rows) {
        let rows = [];
        if (this_rows) {
            rows = this_rows;
        } else {
            $('#jawc_table #list tr').each(function (i, row) {
                rows.push(row);
            });
        }

        $('#pagination').pagination({
            dataSource: rows,
            pageSize: 20,
            showGoInput: true,
            showGoButton: true,
            className: 'paginationjs-theme-blue paginationjs-big',
            callback: function (data, pagination) {
                tbody.html(data);
                $('a[id^=open_fragment_dir_]').on("click", function () {
                    var open_path = $(this).attr("data-open-path");
                    open_dir(open_path);
                    message_box.empty();
                    message_box.append('<p>Ready!</p>');
                });
                $('a[id^=download_fragment_]').on("click", function () {
                    var download_path = $(this).attr("data-download-path");
                    download_file(download_path);
                });
                $('a[id^=delete_fragment_]').on("click", function () {
                    var open_path = $(this).attr("data-parent-path");
                    var delete_path = $(this).attr("data-delete-path");
                    delete_file_or_dir(delete_path, open_path,false);
                });
            }
        });
        return rows;
    }

    function table_search() {
        $("#jaw_search").on("keyup", function () {
            lds_spiner.show();
            var value = $(this).val().toLowerCase();
            var results = [];
            tbody.empty();
            $.each(this_rows, function (k, v) {
                var str = v.outerHTML;
                if (str.indexOf(value) >= 0) {
                    tbody.append(v);
                    results.push(k);
                }
            });
            if (results.length === 0) {
                tbody.append('<tr><td class="empty" colspan=5>Value not found.</td></tr>');
            }
            if (value === "") {
                tbody.empty();
                this_rows = table_pagination(true);
            }
            lds_spiner.hide();
        });
    }

    function send_data(req_url, data) {
        lds_spiner.show();
        message_box.empty();
        message_box.append('<p>Loading ... </p>');
        var results = $.post(req_url, data, 'json').done(function () {
            if (message_box.hasClass("jaw_init")) {
                message_box.removeClass("jaw_init");
            }
            if (message_box.hasClass("jaw_error")) {
                message_box.removeClass("jaw_error");
            }
            message_box.addClass("jaw_success");

        }).fail(function (error_data) {
            var resp_text = JSON.parse(error_data.responseText);
            alert('Error ' + resp_text.code + ':  ' + resp_text.msg);

            message_box.empty();
            if (message_box.hasClass("jaw_init")) {
                message_box.removeClass("jaw_init");
            }
            if (message_box.hasClass("jaw_success")) {
                message_box.removeClass("jaw_success");
            }
            message_box.addClass("jaw_error");
            message_box.append('<p>Error ' + resp_text.code + ':  ' + resp_text.msg + '</p>');

        }).always(function () {
            console.log('my_send_to_finish');
            lds_spiner.hide();
        });
        return results;
    }

    function renderFileRow(data) {
        var $link = $('<a id="open_fragment_' + (data.is_dir ? 'dir_' : 'file_') + data.name + '" class="name" />')
                .attr('data-open-path', data.is_dir ? data.path : './' + data.path)
                //.attr('href', '#')
                .text(data.name);
        var allow_direct_link = req_allow_direct_link;
        if (!data.is_dir && !allow_direct_link)
            $link.css('pointer-events', 'none');
        var $dl_link = $('<a id="download_fragment_' + (data.is_dir ? 'dir_' : 'file_') + data.name + '"/>').attr('data-download-path', data.path)
                .addClass('download').text('download');
        var $delete_link = $('<a id="delete_fragment_' + (data.is_dir ? 'dir_' : 'file_') + data.name + '"/>').attr('data-delete-path', data.path).attr('data-parent-path', data.parent_path).addClass('delete').text('delete');
        var perms = [];
        if (data.is_readable)
            perms.push('read');
        if (data.is_writable)
            perms.push('write');
        if (data.is_executable)
            perms.push('exec');
        var $html = $('<tr />')
                .addClass(data.is_dir ? 'is_dir' : '')
                .append($('<td class="first" />').append($link))
                .append($('<td/>').attr('data-sort', data.is_dir ? -1 : data.size)
                        .html($('<span class="size" />').text(formatFileSize(data.size))))
                .append($('<td/>').attr('data-sort', data.mtime).text(formatTimestamp(data.mtime)))
                .append($('<td/>').text(perms.join('+')))
                .append($('<td/>').append($dl_link).append(data.is_deleteable ? $delete_link : ''));
        return $html;
    }

    function renderBreadcrumbs(path) {
        var breadcrumb = $('.breadcrumb');
        breadcrumb.empty();
        var current_path = "/";
        $.each(path.split('/'), function (k, v) {
            if (v) {
                var v_as_text = decodeURIComponent(v);
                current_path += v_as_text + "/";
                breadcrumb.append('<li><a id="breadcrumb_dir_' + v_as_text + '" data-open-path="' + current_path + '" >' + v_as_text + '</a></li>');
            }
        });
    }

    function formatTimestamp(unix_timestamp) {
        var m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var d = new Date(unix_timestamp * 1000);
        return [m[d.getMonth()], ' ', d.getDate(), ', ', d.getFullYear(), " ",
            (d.getHours() % 12 || 12), ":", (d.getMinutes() < 10 ? '0' : '') + d.getMinutes(),
            " ", d.getHours() >= 12 ? 'PM' : 'AM'].join('');
    }
    function formatFileSize(bytes) {
        var s = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];
        for (var pos = 0; bytes >= 1000; pos++, bytes /= 1024)
            ;
        var d = Math.round(bytes * 10);
        return pos ? [parseInt(d / 10), ".", d % 10, " ", s[pos]].join('') : bytes + ' bytes';
    }





}));
