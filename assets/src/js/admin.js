import '../scss/admin'

const $ = window.jQuery
let isImportHalted = false
const startButton = $('#open_govpub--start-import')
const haltButton = $('#open_govpub--halt-import')

$(document).on('click', '#open_govpub--start-import', function (e) {
  e.preventDefault()

  startImport()
})

$(document).on('click', '#open_govpub--halt-import', function (e) {
  e.preventDefault()

  outputProgress('Clicked abort, halting import...')

  haltImport()
})

function startImport () {
  resetProgress()
  showProgress()
  outputProgress('Starting...')

  openGovpubImport()
}

function haltImport () {
  haltButton.attr('disabled', true)
  isImportHalted = true
  outputProgress('Import halted.')
}

function outputProgress (progress) {
  $('.open_govpub--progress-output').prepend(`${progress} \n`)
}

function resetProgress () {
  $('.open_govpub--progress-output').empty()
}

function showProgress () {
  $('.open_govpub--progress').show()
}

function hideProgress () {
  $('.open_govpub--progress').hide()
}

function updateProgressBar (progress) {
  $('.open_govpub--import-bar').show()
  $('.open_govpub--import-progress').width(progress)
  $('.open_govpub--import-progress span').html(progress)
}

function handleResponse (response) {
  updateProgressBar(response.progress + '%')

  outputProgress(`Imported ${response.details.status} of ${response.details.max_num} (${response.progress}%)`)

  // Set import string
  if ($('#open_govpub--import-string').length > 0) {
    $('#open_govpub--import-string').html(response.import_string)
  }

  if (!isImportHalted) {
    // Re-run import after 3 second
    setTimeout(fn => openGovpubImport(1), 3000)
  } else {
    // Enable button
    startButton.attr('disabled', false)

    // Hide halt button and progress bar
    $('#open_govpub--halt-import').attr('disabled', false).hide()
    $('.open_govpub--import-bar').hide()

    hideProgress()
  }
}

function openGovpubImport (checked) {
  checked = checked || 0
  startButton.attr('disabled', true)

  $('#open_govpub--halt-import').show()

  if (!checked) {
    outputProgress('Getting current state.')
  }

  $.ajax({
    type: 'post',
    dataType: 'json',
    url: window.open_govpub.ajaxurl,
    data: { action: 'import_open_govpub', checked: checked },
    success: function (response) {
      console.log({ response })

      if (!response || typeof response.status === 'undefined') {
        outputProgress('Error! Something went wrong.')

        return haltImport()
      }

      switch (response.status) {
        case 'running':
          return handleResponse(response)
        case 'done':
          outputProgress('All publications have been imported.')
          return haltImport()
        default:
          outputProgress('An unkown state was encountered.')
          return haltImport()
      }
    },
    error: function (response, status, error) {
      console.log({ response, status, error })
      outputProgress('Error! Something went wrong.')
      outputProgress(`Message: ${status}`)

      return haltImport()
    }
  })
}
