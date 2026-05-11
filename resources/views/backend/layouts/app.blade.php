<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->currentLocale()) }}" dir="{{ language_direction() }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <link type="image/png" href="{{ asset("img/favicon.png") }}" rel="icon" />
    <link href="{{ asset("img/favicon.png") }}" rel="apple-touch-icon" sizes="76x76" />
    <meta name="keyword"     content="{{ setting("meta_keyword") }}" />
    <meta name="description" content="{{ setting("meta_description") }}" />
    <link href="{{ asset("img/favicon.png") }}" rel="shortcut icon" />
    <link type="image/ico"   href="{{ asset("img/favicon.png") }}" rel="icon" />
    <meta name="csrf-token"  content="{{ csrf_token() }}" />
    <title>@yield("title") | {{ config("app.name") }}</title>

    <script src="{{ asset("vendor/jquery/jquery-3.6.4.min.js") }}"></script>
    <link  href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet"/>

    @vite(["resources/sass/app-backend.scss", "resources/js/app-backend.js"])

    <link href="https://fonts.googleapis.com/css?family=Ubuntu&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+Bengali+UI&display=swap" rel="stylesheet" />

    <style>
        /* ── Base ──────────────────────────────────────────────────────────── */
        body { font-family: Ubuntu, 'Noto Sans Bengali UI', Arial, Helvetica, sans-serif; }

        /* ── Shared modal shell ─────────────────────────────────────────────── */
        .sn-modal .modal-content {
            border-radius: 14px; border: none;
            box-shadow: 0 12px 48px rgba(0,0,0,.2);
        }
        .sn-modal .modal-header {
            background: linear-gradient(135deg,#0d6efd,#6610f2);
            color: #fff; border-radius: 14px 14px 0 0; padding: 15px 20px;
        }
        .sn-modal .modal-header .btn-close { filter: invert(1) opacity(.85); }
        .sn-modal .modal-footer {
            border-top: 1px solid #eee; padding: 12px 20px; gap: 8px;
        }

        /* ── Drop zone ──────────────────────────────────────────────────────── */
        .sn-drop {
            border: 2px dashed #0d6efd; border-radius: 10px;
            padding: 30px 20px; text-align: center; cursor: pointer;
            background: #f0f6ff; position: relative;
            transition: background .18s, border-color .18s;
        }
        .sn-drop.drag-over { background: #ddeeff; border-color: #0044cc; }
        .sn-drop .di { font-size: 38px; display: block; margin-bottom: 6px; }
        .sn-drop .dt { font-size: 14px; color: #444; }
        .sn-drop .ds { font-size: 11px; color: #999; margin-top: 3px; }
        .sn-drop input[type=file] {
            position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;
        }

        /* ── Progress bar ───────────────────────────────────────────────────── */
        .sn-prog { display:none; margin:14px 0 6px; }
        .sn-prog .track { background:#e9ecef; border-radius:99px; height:7px; overflow:hidden; }
        .sn-prog .fill  {
            height:100%; width:0%; border-radius:99px;
            background: linear-gradient(90deg,#0d6efd,#6610f2);
            transition: width .12s;
        }
        .sn-prog .lbl   { font-size:11px; color:#777; margin-top:3px; text-align:right; }

        /* ── Result card ────────────────────────────────────────────────────── */
        .sn-result {
            display:none; background:#f6fff7; border:1.5px solid #28a745;
            border-radius:10px; padding:12px 14px; margin-top:12px;
        }
        .sn-result .rf  { font-weight:600; font-size:13px; color:#155724; word-break:break-all; }
        .sn-result .ru  { display:flex; align-items:center; gap:7px; margin-top:7px; }
        .sn-result .ri  {
            flex:1; font-size:12px; border:1px solid #ced4da; border-radius:6px;
            padding:5px 9px; background:#fff; color:#333;
        }
        .sn-result .rc  { white-space:nowrap; font-size:12px; padding:4px 10px; }
        .sn-result .rc.ok { background:#28a745; border-color:#28a745; color:#fff; }

        /* ── Tabs (link modal) ──────────────────────────────────────────────── */
        .sn-tabs { border-bottom:2px solid #dee2e6; margin-bottom:14px; display:flex; gap:2px; }
        .sn-tabs button {
            border:none; border-bottom:2.5px solid transparent;
            background:none; padding:7px 14px; font-size:13px;
            color:#666; margin-bottom:-2px; cursor:pointer;
            transition: color .15s, border-color .15s;
        }
        .sn-tabs button:hover  { color:#0d6efd; }
        .sn-tabs button.active { color:#0d6efd; border-bottom-color:#0d6efd; font-weight:600; }

        /* ── Edit-mode amber badge ────────────────────────────────────────────*/
        #lm-edit-badge {
            display:none; align-items:center; gap:8px;
            background:#fff8e1; border:1px solid #ffc107;
            border-radius:8px; padding:8px 12px; margin-bottom:14px; font-size:13px;
        }
        #lm-edit-badge .eb-url {
            flex:1; font-size:12px; color:#555;
            overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
        }

        /* ── Recent uploads list ────────────────────────────────────────────── */
        #recentUploadsWrap       { margin-top:16px; }
        #recentUploadsWrap h6    { font-size:12px; color:#888; margin-bottom:7px; }
        .ru-item {
            display:flex; align-items:center; gap:9px; padding:6px 10px;
            border-radius:7px; background:#f8f9fa; border:1px solid #e9ecef;
            margin-bottom:5px; font-size:12px;
        }
        .ru-item .ri  { font-size:18px; flex-shrink:0; }
        .ru-item .rn  { flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#333; }
        .ru-item .ra  { display:flex; gap:4px; flex-shrink:0; }
        .ru-item .ra button { font-size:11px; padding:2px 7px; }

        /* ── Image preview ──────────────────────────────────────────────────── */
        #uploadPreview { display:none; margin-top:10px; text-align:center; }
        #uploadPreview img {
            max-height:110px; max-width:100%; border-radius:6px; border:1px solid #dee2e6;
        }

        /* Summernote inline upload progress */
        .summernote-upload-progress { display:none; height:4px; background:#e9ecef; overflow:hidden; }
        .summernote-upload-progress .bar { height:100%; width:0%; background:#0d6efd; transition:width .15s; }
        .summernote-upload-progress.active { display:block; }
    </style>

    @stack("after-styles")
    <x-google-analytics />
    @livewireStyles
</head>

<body>
    <x-selected-theme />
    @include("backend.includes.sidebar")

    <div class="wrapper d-flex flex-column min-vh-100">
        @include("backend.includes.header")
        <div class="body flex grow">
            <div class="container-lg px-4 py-2">
                @include("flash::message")
                @include("backend.includes.errors")
                @yield("content")
            </div>
        </div>
        <x-backend.includes.footer />
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════
         1.  FILE UPLOAD MODAL  (toolbar "📎 Upload" button)
         ══════════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade sn-modal" id="fileUploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📎 Upload File &amp; Get URL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">

                    <div id="uploadDropZone" class="sn-drop">
                        <input type="file" id="modalFileInput" accept="*/*" />
                        <span class="di">☁️</span>
                        <div class="dt"><strong>Click to browse</strong> or drag &amp; drop</div>
                        <div class="ds">Images · PDF · Word · Excel · Video · Audio · ZIP · any file (max 20 MB)</div>
                    </div>

                    <div id="modalProgressWrap" class="sn-prog">
                        <div class="track"><div class="fill" id="modalProgressFill"></div></div>
                        <div class="lbl"  id="modalProgressLabel">0%</div>
                    </div>

                    <div id="uploadResultCard" class="sn-result">
                        <div class="rf" id="resultFilename"></div>
                        <div class="ru">
                            <input type="text" class="ri" id="resultUrlInput" readonly />
                            <button class="btn btn-outline-secondary rc" id="btnCopyUrl">📋 Copy</button>
                        </div>
                        <div id="uploadPreview"><img id="uploadPreviewImg" src="" alt="preview" /></div>
                        <div class="mt-3 d-flex gap-2 flex-wrap">
                            <button class="btn btn-success btn-sm"         id="btnInsertIntoEditor">✅ Insert into Editor</button>
                            <button class="btn btn-outline-primary btn-sm" id="btnUploadAnother">⬆️ Upload Another</button>
                            <a href="#" target="_blank" class="btn btn-outline-secondary btn-sm" id="btnOpenFile">🔗 Open</a>
                        </div>
                    </div>

                    <div id="recentUploadsWrap" style="display:none;">
                        <h6>📁 Uploaded this session</h6>
                        <div id="recentUploadsList"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════
         2.  INSERT / EDIT LINK MODAL
         ─────────────────────────────────────────────────────────────────────
         CREATE mode  (cursor not on a link)
           • Title auto-filled from selected text
           • Tab A: Upload file  →  URL + filename fill in automatically
           • Tab B: Paste any URL manually

         EDIT mode  (cursor inside an existing <a>)
           • Amber badge shows the current href
           • Title + URL pre-populated from the existing link
           • User may change title, swap URL (upload or paste), toggle new-tab
           • "Remove Link" strips the <a> and leaves plain text
         ══════════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade sn-modal" id="linkInsertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:540px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lm-title-text">🔗 Insert Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">

                    {{-- Edit-mode amber badge --}}
                    <div id="lm-edit-badge">
                        <span>✏️ Editing link →</span>
                        <span class="eb-url" id="lm-current-url"></span>
                        <button class="btn btn-outline-danger btn-sm" id="lm-btn-remove-link">
                            🗑 Remove Link
                        </button>
                    </div>

                    {{-- Title --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;" for="linkModalTitle">
                            Text to display
                            <span class="text-muted fw-normal" style="font-size:11px;">
                                (leave blank to use filename / URL)
                            </span>
                        </label>
                        <input type="text" id="linkModalTitle" class="form-control form-control-sm"
                               placeholder="e.g.  Download Annual Report" />
                    </div>

                    {{-- Tabs --}}
                    <div class="sn-tabs" id="linkModalTabs">
                        <button class="active" data-tab="upload" id="tabLinkUpload">☁️ Upload File</button>
                        <button               data-tab="manual" id="tabLinkManual">🔗 Paste URL</button>
                    </div>

                    {{-- TAB: Upload --}}
                    <div id="linkTabUpload">
                        <div id="linkDropZone" class="sn-drop">
                            <input type="file" id="linkFileInput" accept="*/*" />
                            <span class="di">☁️</span>
                            <div class="dt"><strong>Click to browse</strong> or drag &amp; drop</div>
                            <div class="ds">Images · PDF · Word · Excel · Video · Audio · ZIP · any file (max 20 MB)</div>
                        </div>
                        <div id="linkUploadProgress" class="sn-prog">
                            <div class="track"><div class="fill" id="linkProgressFill"></div></div>
                            <div class="lbl"  id="linkProgressLabel">0%</div>
                        </div>
                        <div id="linkUploadResult" class="sn-result">
                            <div class="rf" id="linkResultFilename"></div>
                            <div class="ru">
                                <input type="text" class="ri" id="linkResultUrl" readonly />
                                <button class="btn btn-outline-secondary btn-sm rc" id="linkBtnCopy">📋 Copy</button>
                            </div>
                            <button class="btn btn-outline-primary btn-sm mt-2" id="linkBtnUploadAnother">
                                ⬆️ Upload Another
                            </button>
                        </div>
                    </div>

                    {{-- TAB: Manual URL --}}
                    <div id="linkTabManual" style="display:none;">
                        <label class="form-label fw-semibold" style="font-size:13px;" for="linkManualUrl">URL</label>
                        <input type="url" id="linkManualUrl" class="form-control form-control-sm"
                               placeholder="https://example.com/file.pdf" />
                        <div class="text-muted mt-1" style="font-size:11px;">Include full URL with https://</div>
                    </div>

                    {{-- Options --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="linkOpenNewTab" checked />
                        <label class="form-check-label" style="font-size:13px;" for="linkOpenNewTab">
                            Open in new tab
                        </label>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary   btn-sm" id="linkBtnInsert">🔗 Insert Link</button>
                </div>
            </div>
        </div>
    </div>


    @livewireScripts
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
    <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
    @stack("after-scripts")

    <script>
    (function ($) {
        'use strict';

        /* ── Config ──────────────────────────────────────────────────────── */
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        var UPLOAD_URL = '{{ route("editor.upload") }}';

        /* ── MIME helpers ────────────────────────────────────────────────── */
        var MIME = {
            image : ['image/png','image/jpeg','image/jpg','image/gif','image/webp','image/svg+xml','image/bmp'],
            video : ['video/mp4','video/webm','video/ogg','video/avi','video/quicktime'],
            audio : ['audio/mpeg','audio/mp3','audio/ogg','audio/wav','audio/aac','audio/flac'],
        };
        function isType(m, list) { return list.indexOf(m) > -1; }
        function fileIcon(m) {
            if (!m)                                                    return '📎';
            if (isType(m, MIME.image))                                 return '🖼️';
            if (isType(m, MIME.video))                                 return '🎬';
            if (isType(m, MIME.audio))                                 return '🎵';
            if (m === 'application/pdf')                               return '📄';
            if (m.includes('word'))                                    return '📝';
            if (m.includes('excel') || m.includes('sheet'))           return '📊';
            if (m.includes('powerpoint') || m.includes('pres'))       return '📑';
            if (m.includes('zip')   || m.includes('rar'))             return '🗜️';
            return '📎';
        }

        /* ── Livewire sync ───────────────────────────────────────────────── */
        function syncLivewire(val) {
            var el = document.getElementById('content-hidden');
            if (!el) return;
            var s = Object.getOwnPropertyDescriptor(window.HTMLInputElement.prototype, 'value').set;
            s.call(el, val);
            el.dispatchEvent(new Event('input', { bubbles: true }));
        }

        /* ── Clipboard copy with button feedback ─────────────────────────── */
        function copyText(text, $btn) {
            navigator.clipboard.writeText(text).then(function () {
                if (!$btn) return;
                var orig = $btn.text();
                $btn.text('✅ Copied!').addClass('ok');
                setTimeout(function () { $btn.text(orig).removeClass('ok'); }, 2000);
            });
        }

        /* ── Bind drag-drop to a drop zone ──────────────────────────────── */
        function bindDrop(zone, cb) {
            $(document)
                .on('dragover',  zone, function (e) { e.preventDefault(); $(this).addClass('drag-over'); })
                .on('dragleave', zone, function ()  { $(this).removeClass('drag-over'); })
                .on('drop',      zone, function (e) {
                    e.preventDefault(); $(this).removeClass('drag-over');
                    var f = e.originalEvent.dataTransfer.files[0];
                    if (f) cb(f);
                });
        }

        /* ── Reusable AJAX upload ────────────────────────────────────────── */
        // opts: { $drop, $prog, $fill, $lbl, $result, $name, $url, $input, onSuccess }
        function doUpload(file, opts) {
            var fd = new FormData();
            fd.append('file', file);

            opts.$drop.hide(); opts.$result.hide();
            opts.$prog.show(); opts.$fill.css('width','0%'); opts.$lbl.text('0%');

            $.ajax({
                url: UPLOAD_URL, type: 'POST', data: fd,
                cache: false, contentType: false, processData: false,
                xhr: function () {
                    var x = new XMLHttpRequest();
                    x.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            var p = Math.round(e.loaded / e.total * 100);
                            opts.$fill.css('width', p + '%'); opts.$lbl.text(p + '%');
                        }
                    });
                    return x;
                },
                success: function (res) {
                    opts.$prog.hide();
                    opts.$name.text(fileIcon(res.mimeType) + '  ' + res.name);
                    opts.$url.val(res.url);
                    opts.$result.show();
                    sessionUploads.push(res);
                    refreshRecentList();
                    if (opts.onSuccess) opts.onSuccess(res);
                },
                error: function (xhr) {
                    opts.$prog.hide(); opts.$drop.show();
                    var msg = 'Upload failed.';
                    try {
                        var j = JSON.parse(xhr.responseText);
                        msg = (j.errors ? Object.values(j.errors).flat().join('\n') : j.message) || msg;
                    } catch (e) {}
                    alert('❌ ' + msg);
                }
            });
        }

        /* ── Session upload list ─────────────────────────────────────────── */
        var sessionUploads = [];
        function refreshRecentList() {
            if (!sessionUploads.length) { $('#recentUploadsWrap').hide(); return; }
            $('#recentUploadsWrap').show();
            $('#recentUploadsList').html(
                sessionUploads.slice().reverse().map(function (it) {
                    return '<div class="ru-item">' +
                        '<span class="ri">' + fileIcon(it.mimeType) + '</span>' +
                        '<span class="rn" title="' + it.url + '">' + it.name + '</span>' +
                        '<div class="ra">' +
                            '<button class="btn btn-outline-secondary btn-sm ru-copy"' +
                                ' data-url="' + it.url + '">📋</button>' +
                            '<button class="btn btn-success btn-sm ru-insert"' +
                                ' data-url="'  + it.url      + '"' +
                                ' data-name="' + it.name     + '"' +
                                ' data-mime="' + it.mimeType + '">Insert</button>' +
                        '</div></div>';
                }).join('')
            );
        }

        /* ── Insert file/media into Summernote ───────────────────────────── */
        function insertFileIntoEditor(data) {
            var url = data.url, name = data.name, m = data.mimeType;
            if (isType(m, MIME.image)) {
                $('#content').summernote('insertImage', url, name);
            } else if (isType(m, MIME.video)) {
                $('#content').summernote('pasteHTML',
                    '<div contenteditable="false" style="margin:8px 0;">' +
                    '<video controls style="max-width:100%;border-radius:4px;">' +
                    '<source src="' + url + '" type="' + m + '"></video>' +
                    '<div style="font-size:12px;color:#666;margin-top:2px;">' + fileIcon(m) + ' ' + name + '</div></div>');
            } else if (isType(m, MIME.audio)) {
                $('#content').summernote('pasteHTML',
                    '<div contenteditable="false" style="margin:8px 0;">' +
                    '<audio controls style="width:100%;"><source src="' + url + '" type="' + m + '"></audio>' +
                    '<div style="font-size:12px;color:#666;margin-top:2px;">' + fileIcon(m) + ' ' + name + '</div></div>');
            } else {
                $('#content').summernote('pasteHTML',
                    '<a href="' + url + '" target="_blank" download="' + name + '" style="' +
                    'display:inline-flex;align-items:center;gap:6px;padding:6px 12px;' +
                    'background:#f8f9fa;border:1px solid #dee2e6;border-radius:6px;' +
                    'text-decoration:none;color:#212529;font-size:14px;margin:4px 0;">' +
                    fileIcon(m) + ' ' + name + '</a>');
            }
            syncLivewire($('#content').summernote('code'));
        }

        /* ══════════════════════════════════════════════════════════════════
           UPLOAD MODAL  (📎 Upload toolbar button)
        ══════════════════════════════════════════════════════════════════ */
        var upOpts = {
            $drop   : $('#uploadDropZone'),
            $prog   : $('#modalProgressWrap'),
            $fill   : $('#modalProgressFill'),
            $lbl    : $('#modalProgressLabel'),
            $result : $('#uploadResultCard'),
            $name   : $('#resultFilename'),
            $url    : $('#resultUrlInput'),
            $input  : $('#modalFileInput'),
            onSuccess: function (res) {
                isType(res.mimeType, MIME.image)
                    ? ($('#uploadPreviewImg').attr('src', res.url), $('#uploadPreview').show())
                    : $('#uploadPreview').hide();
                $('#btnOpenFile').attr('href', res.url);
                _lastUpload = res;
            }
        };
        var _lastUpload = null;

        function resetUploadModal() {
            _lastUpload = null;
            upOpts.$drop.show(); upOpts.$result.hide(); upOpts.$prog.hide();
            upOpts.$fill.css('width','0%'); upOpts.$lbl.text('0%'); upOpts.$input.val('');
            $('#uploadPreview').hide();
            $('#btnCopyUrl').text('📋 Copy').removeClass('ok');
        }

        $(document).on('change', '#modalFileInput', function () {
            if (this.files[0]) doUpload(this.files[0], upOpts);
        });
        bindDrop('#uploadDropZone', function (f) { doUpload(f, upOpts); });

        $(document).on('click', '#btnCopyUrl',          function () { copyText($('#resultUrlInput').val(), $(this)); });
        $(document).on('click', '#btnInsertIntoEditor', function () {
            if (_lastUpload) { insertFileIntoEditor(_lastUpload); $('#fileUploadModal').modal('hide'); }
        });
        $(document).on('click', '#btnUploadAnother',    resetUploadModal);
        $('#fileUploadModal').on('hidden.bs.modal',      resetUploadModal);

        $(document).on('click', '.ru-copy',   function () { copyText($(this).data('url')); });
        $(document).on('click', '.ru-insert', function () {
            insertFileIntoEditor({ url: $(this).data('url'), name: $(this).data('name'), mimeType: $(this).data('mime') });
            $('#fileUploadModal').modal('hide');
        });

        /* ══════════════════════════════════════════════════════════════════
           LINK MODAL  (custom link toolbar button)
           State machine:  CREATE  |  EDIT
        ══════════════════════════════════════════════════════════════════ */
        var lm = {
            ctx       : null,    // Summernote context
            editNode  : null,    // <a> element in edit mode (null = create)
            tab       : 'upload',
            uploadUrl : null,
            uploadName: null,
        };

        var lmOpts = {
            $drop   : $('#linkDropZone'),
            $prog   : $('#linkUploadProgress'),
            $fill   : $('#linkProgressFill'),
            $lbl    : $('#linkProgressLabel'),
            $result : $('#linkUploadResult'),
            $name   : $('#linkResultFilename'),
            $url    : $('#linkResultUrl'),
            $input  : $('#linkFileInput'),
            onSuccess: function (res) {
                lm.uploadUrl  = res.url;
                lm.uploadName = res.name;
                if (!$('#linkModalTitle').val().trim()) $('#linkModalTitle').val(res.name);
            }
        };

        function lmSwitchTab(tab) {
            lm.tab = tab;
            $('#linkModalTabs button').removeClass('active');
            $('#linkModalTabs button[data-tab="' + tab + '"]').addClass('active');
            $('#linkTabUpload').toggle(tab === 'upload');
            $('#linkTabManual').toggle(tab === 'manual');
        }

        function lmReset() {
            lm.editNode = null; lm.uploadUrl = null; lm.uploadName = null;

            $('#lm-title-text').text('🔗 Insert Link');
            $('#lm-edit-badge').hide();
            $('#lm-current-url').text('');
            $('#linkModalTitle').val('');
            $('#linkManualUrl').val('');
            $('#linkOpenNewTab').prop('checked', true);
            $('#linkBtnInsert').text('🔗 Insert Link');
            $('#linkBtnCopy').text('📋 Copy').removeClass('ok');

            lmOpts.$drop.show(); lmOpts.$result.hide(); lmOpts.$prog.hide();
            lmOpts.$fill.css('width','0%'); lmOpts.$lbl.text('0%'); lmOpts.$input.val('');

            lmSwitchTab('upload');
        }

        // ── Open: detect CREATE vs EDIT ─────────────────────────────────
        function lmOpen(ctx) {
            lm.ctx = ctx;
            lmReset();

            // Find the nearest <a> ancestor from the cursor position
            var range  = ctx.invoke('createRange');
            var anchor = null;
            if (range) {
                var node = range.sc;
                while (node && node !== document) {
                    if (node.nodeName === 'A') { anchor = node; break; }
                    node = node.parentNode;
                }
            }

            if (anchor) {
                // ── EDIT MODE ────────────────────────────────────────────
                lm.editNode = anchor;
                var href  = anchor.getAttribute('href') || '';
                var text  = (anchor.textContent || '').trim();
                var isNew = anchor.getAttribute('target') === '_blank';

                $('#lm-title-text').text('✏️ Edit Link');
                $('#lm-edit-badge').css('display','flex');
                $('#lm-current-url').text(href);
                $('#linkModalTitle').val(text);
                $('#linkManualUrl').val(href);
                $('#linkOpenNewTab').prop('checked', isNew);
                $('#linkBtnInsert').text('✅ Update Link');

                lmSwitchTab('manual');   // show URL tab first in edit mode

            } else {
                // ── CREATE MODE ──────────────────────────────────────────
                if (range && !range.isCollapsed()) {
                    var sel = range.toString().trim();
                    if (sel) $('#linkModalTitle').val(sel);
                }
                lmSwitchTab('upload');
            }

            $('#linkInsertModal').modal('show');
        }

        // Tab switch
        $(document).on('click', '#linkModalTabs button', function () { lmSwitchTab($(this).data('tab')); });

        // File input + drag-drop
        $(document).on('change', '#linkFileInput', function () {
            if (this.files[0]) doUpload(this.files[0], lmOpts);
        });
        bindDrop('#linkDropZone', function (f) { doUpload(f, lmOpts); });

        $(document).on('click', '#linkBtnCopy', function () {
            copyText($('#linkResultUrl').val(), $(this));
        });
        $(document).on('click', '#linkBtnUploadAnother', function () {
            lm.uploadUrl = null; lm.uploadName = null;
            lmOpts.$result.hide(); lmOpts.$drop.show(); lmOpts.$input.val('');
        });

        // Remove link (edit mode) — unwrap <a>, keep text
        $(document).on('click', '#lm-btn-remove-link', function () {
            if (lm.editNode && lm.ctx) {
                $(lm.editNode).replaceWith(document.createTextNode(lm.editNode.textContent));
                syncLivewire($('#content').summernote('code'));
            }
            $('#linkInsertModal').modal('hide');
        });

        // Insert / Update
        $(document).on('click', '#linkBtnInsert', function () {
            var finalUrl = lm.tab === 'upload'
                ? (lm.uploadUrl || '')
                : ($('#linkManualUrl').val().trim() || (lm.editNode ? (lm.editNode.getAttribute('href') || '') : ''));

            if (!finalUrl) { alert('⚠️ Please upload a file or enter a URL first.'); return; }

            var title  = $('#linkModalTitle').val().trim() || lm.uploadName || finalUrl;
            var newTab = $('#linkOpenNewTab').is(':checked');

            if (lm.editNode) {
                // ── UPDATE existing <a> in place ─────────────────────────
                lm.editNode.setAttribute('href', finalUrl);
                lm.editNode.textContent = title;
                if (newTab) {
                    lm.editNode.setAttribute('target', '_blank');
                    lm.editNode.setAttribute('rel', 'noopener noreferrer');
                } else {
                    lm.editNode.removeAttribute('target');
                    lm.editNode.removeAttribute('rel');
                }
            } else {
                // ── INSERT new <a> ───────────────────────────────────────
                var tAttr = newTab ? ' target="_blank" rel="noopener noreferrer"' : '';
                lm.ctx.invoke('pasteHTML', '<a href="' + finalUrl + '"' + tAttr + '>' + title + '</a>');
            }
            syncLivewire($('#content').summernote('code'));
            $('#linkInsertModal').modal('hide');
        });

        $('#linkInsertModal').on('hidden.bs.modal', function () { lmReset(); lm.ctx = null; });


        /* ══════════════════════════════════════════════════════════════════
           SUMMERNOTE INIT
        ══════════════════════════════════════════════════════════════════ */

        var LFMButton = function (ctx) {
            return $.summernote.ui.button({
                contents : '<i class="note-icon-picture"></i>',
                tooltip  : 'Insert image via File Manager',
                click    : function () {
                    window.open('/laravel-filemanager?type=image', 'FileManager', 'width=900,height=600');
                    window.SetUrl = function (items) {
                        items.forEach(function (it) { ctx.invoke('insertImage', it.url); });
                    };
                }
            }).render();
        };

        var UploadFileButton = function () {
            return $.summernote.ui.button({
                contents : '📎 Upload',
                tooltip  : 'Upload any file — get URL or insert directly',
                click    : function () { resetUploadModal(); refreshRecentList(); $('#fileUploadModal').modal('show'); }
            }).render();
        };

        // Replaces default link button — handles both INSERT and EDIT
        var CustomLinkButton = function (ctx) {
            return $.summernote.ui.button({
                contents : '<i class="note-icon-link"></i>',
                tooltip  : 'Insert / Edit Link',
                click    : function () { lmOpen(ctx); }
            }).render();
        };

        // Inline drag-drop → server upload (prevents base64 bloat)
        function uploadDirect(file) {
            var fd = new FormData();
            fd.append('file', file);
            $.ajax({
                url: UPLOAD_URL, type: 'POST', data: fd,
                cache: false, contentType: false, processData: false,
                success: function (res) { insertFileIntoEditor(res); },
                error  : function ()    { alert('Upload failed.'); }
            });
        }

        function initSummernote() {
            var $el = $('#content');
            if (!$el.length) return;
            if ($el.next('.note-editor').length) $el.summernote('destroy');

            $el.summernote({
                height  : 300,
                toolbar : [
                    ['style',  ['style']],
                    ['font',   ['fontname', 'fontsize', 'bold', 'underline', 'clear']],
                    ['color',  ['color']],
                    ['para',   ['ul', 'ol', 'paragraph']],
                    ['table',  ['table']],
                    ['insert', ['customlink', 'lfm', 'uploadfile', 'video']],
                    ['view',   ['codeview', 'undo', 'redo', 'help']],
                ],
                buttons : {
                    lfm        : LFMButton,
                    uploadfile : UploadFileButton,
                    customlink : CustomLinkButton,
                },
                callbacks : {
                    onImageUpload : function (files) {
                        for (var i = 0; i < files.length; i++) uploadDirect(files[i]);
                    },
                    onChange : function (c) { syncLivewire(c); },
                    onBlur   : function ()  { syncLivewire($el.summernote('code')); }
                }
            });

            var hidden = document.getElementById('content-hidden');
            if (hidden && hidden.value) $el.summernote('code', hidden.value);
            if ($('#button-image').length) $('#button-image').filemanager('image');
        }

        $(document).ready(initSummernote);

        document.addEventListener('livewire:init', function () {
            Livewire.hook('morph.updated', function () { setTimeout(initSummernote, 100); });
        });

        document.addEventListener('livewire:initialized', function () {
            Livewire.on('reset-editor', function () {
                var $el = $('#content');
                if ($el.length && $el.next('.note-editor').length) $el.summernote('code', '');
                syncLivewire('');
            });
        });

    })(window.jQuery);
    </script>
</body>
</html>