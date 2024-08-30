@extends('layouts.app')

@section('content')
    <div class="header-content">
        <div class="tag-name">
            CEFR
        </div>

        <button class="add-client" id="show_filters"><i class="fa fa-reorder"></i>Filters</button>
    </div>

    <div class="panel" id="filters">
        <div class="panel-heading">
            Filter Tasks
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4 form-group">
                    {!! Form::select('category', $categories, null, ['class' => 'form-control sel-status column_filter full-height-select', 'id' => 'category_filter', 'placeholder' => 'All Catagories']) !!}
                </div>
                <div class="col-md-1 form-group">
                    {!! Form::select('level', $levels, null, ['class' => 'form-control sel-status column_filter', 'id' => 'level_filter', 'placeholder' => 'All Levels']) !!}
                </div>
                <div class="col-md-2 form-group">
                    <button class="btn btn-primary text-uppercase col-xs-12 pull-right" id="reset_filters">
                        Show all
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
            <table id="cefr-table" class="table table-bordered table-striped display responsive nowrap"
                   data-route="cefr">
                <thead>
                <tr>
                    <th>Category</th>
                    <th>Level</th>
                    <th class="hidden">Order</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection


@section('aside-right')
    <aside id="edit-modal" class="control-sidebar control-sidebar-add">
        <div class="loading">
            <div class="loading-wheel"></div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <button type="button" class="close" data-toggle="control-sidebar">&times;</button>
                <h1>
                    Edit CEFR
                </h1>
                <p></p>
            </div>
            <div class="panel-body">
                {!! Form::open(['id' => 'edit-form']) !!}
                {!! Form::hidden('cefr_id', null, ['id' => 'cefr_id']) !!}
                <div class="form-group">
                    {!! Form::label('Name', '') !!}
                    {!! Form::input('text', 'category', '', ['class' => 'form-control sel-status', 'id' => 'cefr-name', 'required' => true]) !!}
                </div>
                @foreach(config('languages') as $key => $language)
                    <div class="form-group">
                        {!! Form::label($key, $language) !!}
                        {!! Form::textarea('translations[' . $key . ']', null, ['class' => 'form-control sel-status', 'id' => $key, 'placeholder' => $language . ' Translation Text...', 'required' => true]) !!}
                    </div>
                @endforeach
                <div class="form-group">
                    {!! Form::submit('Save CEFR', ['class' => 'btn btn-danger', 'id' => 'edit-submit-button']) !!}
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </aside>
@endsection


@section('footer')
    <script src="{{ url('js/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script>

        $(document).ready(function () {
            var edit_modal = $("#edit-modal");
            edit_modal.find('textarea').wysihtml5({
                stylesheets: ["/css/styles.min.css"]
            });

            $('select#category_filter').select2({
                placeholder: 'All Categories',
                allowClear: true
            }).maximizeSelect2Height();

            $('select#level_filter').select2({
                placeholder: 'All Levels',
                allowClear: true
            });

            document.getElementById("reset_filters").onclick = function () {
                $('.column_filter').val('').trigger('change')
            };

            var element = $('#cefr-table');
            var route = element.attr('data-route');
            var title = 'CEFR';

            element.on('click', 'a.edit-button', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');

                document.getElementById("edit-form").reset();
                document.getElementById('edit-modal').scrollTop = 0;
                $('#edit-modal .loading').addClass('active');
                $('#edit-modal').addClass('control-sidebar-open');
                $('body').addClass('open');

                $.ajax({
                    url: '/' + route + '/update-form-data/' + id,
                    type: 'GET',
                    success: function (response) {
                        result = JSON.parse(response);
                        var modal = $('#edit-modal');

                        modal.find('.panel-heading p').html('<strong>' + result.reference.level + '</strong>');
                        modal.find('#cefr-name').val(result.reference.category);
                        modal.find('#cefr_id')[0].value = result.reference.id;

                        $.each(result.translations, function (key, language) {
                            modal.find('textarea#' + key).parent().find('iframe').contents().find('.wysihtml5-editor').html(language.value).focus();
                        });

                        $('#edit-modal .loading').removeClass('active');
                    },
                    error: function (response) {
                        swal({
                            type: 'error',
                            title: 'Your ' + title + ' has not been updated.'
                        });
                    }
                });
            });

            $('#edit-form #edit-submit-button').on('click', function (e) {
                var _this = $(this);
                var form = $(this).parents('form:first');
                form.show();
                form.validate({
                    submitHandler: function (form, e, _this) {
                        e.preventDefault();
                        var id = $('#edit-form').find('#cefr_id')[0].value;

                        $('#edit-modal').removeClass('control-sidebar-open');

                        $.ajax({
                            url: '/' + route + '/update/' + id,
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: new FormData(document.getElementById('edit-form')),
                            cache: false,
                            contentType: false,
                            processData: false,
                            dataType: 'json',
                            success: function (res) {
                                $('body').removeClass('open');
                                if (res.resType === 'Success') {
                                    swal({
                                        type: 'success',
                                        title: 'Your ' + title + ' has been updated.',
                                        showConfirmButton: false,
                                        timer: 2500
                                    });
                                    table.ajax.reload(null, false);
                                } else {
                                    var errors = '';
                                    $.each(res.resMotive, function (key, value) {
                                        errors += value[0] + '<br/>';
                                    });
                                    swal(
                                        'Error!',
                                        errors,
                                        'error'
                                    );
                                }
                            },
                            error: function (response) {
                                $('body').removeClass('open');
                                swal({
                                    type: 'error',
                                    title: 'Your ' + title + ' has not been updated.'
                                });
                            }
                        });
                    }
                });
            });

            function generateDataTable(element, route) {
                var filters = {};
                $('.column_filter').each(function () {
                    filters[this.name] = this.value;
                });

                return element.DataTable({
                    destroy: true,
                    paging: true,
                    lengthChange: false,
                    pageLength: 30,
                    searching: false,
                    ordering: true,
                    info: true,
                    autoWidth: true,
                    processing: true,
                    serverSide: true,
                    order: [ 2 ],
                    ajax: {
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: '/' + route + '-data',
                        type: "POST",
                        data: {
                            filters: filters
                        }
                    },
                    createdRow: function createdRow(row, data) {
                        $(row).attr('data-id', data.id);
                    },
                    "fnDrawCallback": function () {
                        if (element.parent().find('span > .paginate_button').length > 1) {
                            element.parent().find('.dataTables_paginate')[0].style.display = "block";
                            element.parent().find('.dataTables_info')[0].style.display = "block";
                        } else if(element.parent().find('span > .paginate_button').length >= 0) {
                            element.parent().find('.dataTables_paginate')[0].style.display = "none";
                            element.parent().find('.dataTables_info')[0].style.display = "none";
                        }
                    },
                    columns: [
                        {
                            data: 'category',
                            name: 'category'
                        },
                        {
                            data: 'level',
                            name: 'level'
                        },
                        {
                            data: 'order',
                            name: 'order',
                            'visible': false,
                        },
//                        {
//                            data: 'description',
//                            name: 'description'
//                        },
                        {
                            data: null,
                            className: "actions",
                            render: function (data, type, row) {

                                var html =  '<a href="#" class="edit-button" data-id="' + row.id + '"><span class="fa fa-pencil"></span></a>';
                                return html;
                            },
                            defaultContent: '',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    "fnRowCallback": function fnRowCallback(nRow, aData) {
//                        $('td:eq(2)', nRow).html(
//                            aData.description.replace(/&lt;/g, "<").replace(/&gt;/g, ">")
//                        );
                        return nRow;
                    }
                });
            }

            var table = generateDataTable(element, route);

            $('input.column_filter, select.column_filter').on('change', function () {
                generateDataTable(element, route);
            });

            crudDataTable(table, element, route, title);

        });
        (function ($) {
            "use strict";

            // We can find these elements now, since the properties we check on them are
            // all via methods that are recalculated each time.
            var $window = $(window);
            var $document = $(document);

            // @param {Object} options The options object passed in when this plugin is
            //   initialized
            // @param {Boolean} dropdownDownwards True iff the dropdown is rendered
            //   downwards (Select2 sometimes renders the options upwards to better fit on
            //   a page)
            // @return {Object} The options passed in, combined with defaults. Keys are:
            //   - cushion: The number of pixels between the edge of the dropdown and the
            //              edge of the viewable window. [Default: 10, except when a
            //              horizontal scroll bar would interfere, in which case it's 30.]
            //              NOTE: If a value is passed in, no adjustments for possible
            //              scroll bars are made.
            var settings = function (options, dropdownDownwards) {
                return $.extend({
                    cushion: (
                            dropdownDownwards && $document.width() > $window.width()
                    ) ? 30 : 10
                }, options);
            };

            // @param {String} id The DOM element ID for the original <select> node
            // @param {jQuery object} $select2Results The DOM element with class
            //   "select2-results"
            // @param {jQuery object} $grandparent The grandparent object of the
            //   $select2Results object
            // @param {Object} options The options object passed in when this plugin is
            //   initialized
            // @param {Boolean} dropdownDownwards True iff the dropdown is rendered
            //   downwards (Select2 sometimes renders the options upwards to better fit on
            //   a page)
            // @return {Number} the maximum height of the Select2 results box to display
            var computeMaxHeight = function (
                    id, $select2Results, $grandparent, options, dropdownDownwards
            ) {
                var height;
                var resultsBoxMiscellaniaHeight;
                var widgetBoxOffset;

                if (dropdownDownwards) {
                    // When the dropdown appears downwards, the formula is:
                    //   visible window size
                    // + out-of-window pixels we've scrolled past
                    // - size of content (including offscreen content) above results box
                    // ------------------------------------------
                    //   total height available to us

                    // innerHeight is more accurate across browsers than $(window).height().
                    height = window.innerHeight +
                            $window.scrollTop() -
                            $select2Results.offset().top;
                } else {
                    // When the dropdown appears upwards, the formula is:
                    //   vertical position of the widget (clickable) dropdown box
                    // - out-of-window pixels we've scrolled past
                    // - height of the search box and other content above the actual results
                    //   but in the results box
                    // ------------------------------------------
                    //   total height available to us

                    // Compute the global vertical offset of the widget box (the one with the
                    // downward arrow that the user clicks on to expose options).
                    widgetBoxOffset = $("#select2-" + id + "-container").
                    parent().parent().parent().offset().top;

                    // Compute the height, if any, of search box and other content in the
                    // results box but not part of the results.
                    resultsBoxMiscellaniaHeight = $grandparent.height() -
                            $select2Results.height();
                    height = widgetBoxOffset -
                            $window.scrollTop() -
                            resultsBoxMiscellaniaHeight;
                }

                // Leave a little cushion to prevent the dropdown from
                // rendering off the edge of the viewport.
                return height - settings(options, dropdownDownwards).cushion;
            };

            // Call on a jQuery Select2 element to maximize the height of the dropdown
            // every time it is opened.
            // @param {Object} options The options object passed in when this plugin is
            //   initialized
            $.fn.maximizeSelect2Height = function (options) {
                return this.each(function (_, el) {
                    // Each time the Select2 is opened, resize it to take up as much vertical
                    // space as possible given its position and the current viewport size.
                    $(el).on("select2:open", function () {
                        // We have to put this code block inside a timeout because we determine
                        // whether the dropdown is rendered upwards or downwards via a hack that
                        // looks at the CSS classes, and these aren't set until Select2 has a
                        // chance to render the box, which occurs after this event fires.

                        // The alternative solution that avoids using a timeout would be to
                        // directly modify the document's stylesheets (instead of the styles for
                        // individual elements), but that is both ugly/dangerous and actually
                        // impossible for us because we need to modify the styles of a parent
                        // node of a given DOM node when the parent has no unique ID, which CSS
                        // doesn't have the ability to do.
                        setTimeout(function () {
                            var $select2Results = $("#select2-" + el.id + "-results");
                            var $parent = $select2Results.parent();
                            var $grandparent = $parent.parent();
                            var dropdownDownwards = $grandparent
                                    .hasClass("select2-dropdown--below");

                            var maxHeight = computeMaxHeight(
                                    el.id,
                                    $select2Results,
                                    $grandparent,
                                    options,
                                    dropdownDownwards
                            );

                            // Set the max height of the relevant DOM elements. We use max-height
                            // instead of height directly to correctly handle cases in which there
                            // are only a few elements (we don't want a giant empty dropdown box).
                            $parent.css("max-height", maxHeight);
                            $select2Results.css("max-height", maxHeight);

                            // Select2 corrects the positioning of the results box on scroll, so
                            // we trigger that event here to let it auto-correct. This is done for
                            // the case where the dropdown appears upwards; we adjust its max
                            // height but we also want to move it up further, lest it cover up the
                            // initial dropdown box.
                            $(document).trigger("scroll");
                        });
                    });
                });
            };
        })(jQuery);
    </script>
@endsection
