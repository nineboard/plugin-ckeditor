window.XEeditor.tools.define({
  id: 'editortool/image_resize_tool@image_resize_crop',
  events: {
    iconClick: function (targetEditor, cbAppendToolContent) {
      if (targetEditor.config.perms.upload) {
        var $uploadArea = $(targetEditor.container.$).parent().find('.ckeditor-fileupload-area')
        // var currentFilesSize = parseFloat($uploadArea.find('.currentFilesSize').text());

        var uploadUrl = targetEditor.config.fileUpload.upload_url
        var attachMaxSize = targetEditor.config.attachMaxSize
        var fileMaxSize = targetEditor.config.fileMaxSize
        var extensions = targetEditor.config.extensions

        var cWindow = window.open(window.imageResizeURL.get('popup'), 'createImageResizePopup', 'width=850,height=970,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no')
        console.log('cWindow', cWindow, cWindow.document)
        cWindow.focus()
        var timer = setInterval(checkChild, 500)

        function checkChild () {
          if (cWindow != null && cWindow.closed == false && cWindow.ImageResize && typeof cWindow.ImageResize.init == 'function') {
            cWindow.targetEditor = targetEditor
            cWindow.appendToolContent = cbAppendToolContent
            cWindow.uploadInfo = {
              uploadUrl: uploadUrl,
              attachMaxSize: attachMaxSize,
              fileMaxSize: fileMaxSize,
              extensions: extensions,
              $uploadArea: $uploadArea
            }
            cWindow.ImageResize.init()
            clearInterval(timer)
          }
        }
      }
    },
    elementDoubleClick: function () {

    },
    beforeSubmit: function (targetEditor) {

    },
    editorLoaded: function (targetEditor) {
      if (!targetEditor.config.perms.upload) {
        window.$('.cke_button__imageresizer').addClass('cke_button_disabled')
      }
    }
  },
  props: {
    name: 'ImageResizeTool',
    options: {
      label: '이미지 편집',
      command: 'openImageResizeTool'
    },
    addEvent: {
      doubleClick: false
    }
  }
})
