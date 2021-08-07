(function(Handsontable){
			  var MyEditor = Handsontable.editors.TextEditor.prototype.extend();

			  function customRenderer(hotInstance, td, row, column, prop, value, cellProperties) {
				// ...renderer logic
			  }

			  function customValidator(query, callback) {
				// ...validator logic
				callback(/* Pass `true` or `false` */);
			  }

			  // Register an alias
			  Handsontable.cellTypes.registerCellType('my.custom', {
				editor: MyEditor,
				renderer: customRenderer,
				validator: customValidator,
				// You can add additional options to the cell type based on Handsontable settings
				className: 'my-cell',
				allowInvalid: true,
				// Or you can add custom properties which will be accessible in `cellProperties`
				myCustomCellState: 'complete',
			  });

			})(Handsontable);
			
			
var data = [
    {id: 1, name: 'Ted', isActive: true, color: 'orange', date: '2015-01-01'},
    {id: 2, name: 'John', isActive: false, color: 'black', date: null},
    {id: 3, name: 'Al', isActive: true, color: 'red', date: null},
    {id: 4, name: 'Ben', isActive: false, color: 'blue', date: null}
  ],
  container = document.getElementById('example1'),
  hot1,
  yellowRenderer,
  greenRenderer;

yellowRenderer = function(instance, td, row, col, prop, value, cellProperties) {
  Handsontable.renderers.TextRenderer.apply(this, arguments);
  td.style.backgroundColor = 'yellow';

};

greenRenderer = function(instance, td, row, col, prop, value, cellProperties) {
  Handsontable.renderers.TextRenderer.apply(this, arguments);
  td.style.backgroundColor = 'green';

};

hot1 = new Handsontable(container, {
  data: data,
  startRows: 5,
  colHeaders: true,
  minSpareRows: 1,
  columns: [
    {data: "id", type: 'text'},
    // 'text' is default, you don't actually need to declare it
    {data: "name", renderer: yellowRenderer},
    // use default 'text' cell type but overwrite its renderer with yellowRenderer
    {data: "isActive", type: 'checkbox'},
    {data: "date", type: 'date', dateFormat: 'YYYY-MM-DD'},
    {data: "color",
      type: 'autocomplete',
      source: ["yellow", "red", "orange", "green", "blue", "gray", "black", "white"]
    }
  ],
  cell: [
    {row: 1, col: 0, renderer: greenRenderer}
  ],
  cells: function (row, col, prop) {
    if (row === 0 && col === 0) {
      this.renderer = greenRenderer;
    }
  }
});




/ CustomEditor is a class, inheriting form BaseEditor
class CustomEditor extends BaseEditor {
  prepare(row, col, prop, td, originalValue, cellProperties) {
    // Invoke the original method...
    super.prepare(row, col, prop, td, originalValue, cellProperties);
    // ...and then do some stuff specific to your CustomEditor
    this.customEditorSpecificProperty = 'foo';
  }
}



var CustomEditor = Handsontable.editors.BaseEditor.prototype.extend();

// This won't alter BaseEditor.prototype.beginEditing()
CustomEditor.prototype.beginEditing = function() {};

import Handsontable from 'handsontable';

class PasswordEditor extends Handsontable.editors.TextEditor {
  createElements() {
    super.createElements();

    this.TEXTAREA = this.hot.rootDocument.createElement('input');
    this.TEXTAREA.setAttribute('type', 'password');
    this.TEXTAREA.className = 'handsontableInput';
    this.TEXTAREA.setAttribute('data-hot-input', ''); // Makes the element recognizable by HOT as its own component's element.
    this.textareaStyle = this.TEXTAREA.style;
    this.textareaStyle.width = 0;
    this.textareaStyle.height = 0;

    Handsontable.dom.empty(this.TEXTAREA_PARENT);
    this.TEXTAREA_PARENT.appendChild(this.TEXTAREA);
  }
}

var hot = new Handsontable(document.getElementById('container'), {
  data: someData,
  columns: [
    {
      type: 'text'
    },
    {
      editor: PasswordEditor
      // If you want to use string 'password' instead of passing the actual editor class check out section "Registering editor"
    }
  ]
});

// creating new selectEditor
var SelectEditor = Handsontable.editors.BaseEditor.prototype.extend();

class SelectEditor extends Handsontable.editors.BaseEditor {
  /**
  * Initializes editor instance, DOM Element and mount hooks.
  */
  init() {
    // Create detached node, add CSS class and make sure its not visible
    this.select = this.hot.rootDocument.createElement('SELECT');
    Handsontable.dom.addClass(this.select, 'htSelectEditor');
    this.select.style.display = 'none';
    
    // Attach node to DOM, by appending it to the container holding the table
    this.hot.rootElement.appendChild(this.select);
  }
}

.htSelectEditor {
  /*
   * This hack enables to change <select> dimensions in WebKit browsers
   */
  -webkit-appearance: menulist-button !important;
  position: absolute;
  width: auto;
}

var hot = new Handsontable(document.getElementById('container'), {
  columns: [
    {
      renderer: Handsontable.renderers.NumericRenderer,
      editor: Handsontable.editors.TextEditor,
      validator: Handsontable.validators.NumericValidator
    }
  ]
});

var hot = new Handsontable(document.getElementById('container'), {
  columns: [
    {
      type: 'numeric'
    }
  ]
});



Handsontable.helper.createSpreadsheetData(1000, 1000)


function defaultValueRenderer(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;

  if (args[5] === null && isEmptyRow(instance, row)) {
    args[5] = tpl[col];
    td.style.color = '#999';
  }
  else {
    td.style.color = '';
  }
  Handsontable.renderers.TextRenderer.apply(this, args);
}


ipValidatorRegexp = /^(?:\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b|null)$/;
emailValidator = function (value, callback) {
  setTimeout(function(){
    if (/.+@.+/.test(value)) {
      callback(true);
    }
    else {
      callback(false);
    }
  }, 1000);
};



hot2.updateSettings({
  cells: function (row, col) {
    var cellProperties = {};

    if (hot2.getData()[row][col] === 'Nissan') {
      cellProperties.readOnly = true;
    }

    return cellProperties;
  }
});


  var
    data = [
      {
        title: "<a href='http://www.amazon.com/Professional-JavaScript-Developers-Nicholas-Zakas/dp/1118026691'>Professional JavaScript for Web Developers</a>",
        description: "This <a href='http://bit.ly/sM1bDf'>book</a> provides a developer-level introduction along with more advanced and useful features of <b>JavaScript</b>.",
        comments: "I would rate it &#x2605;&#x2605;&#x2605;&#x2605;&#x2606;",
        cover: "https://handsontable.com/docs/images/examples/professional-javascript-developers-nicholas-zakas.jpg"
      },
      {
        title: "<a href='http://shop.oreilly.com/product/9780596517748.do'>JavaScript: The Good Parts</a>",
        description: "This book provides a developer-level introduction along with <b>more advanced</b> and useful features of JavaScript.",
        comments: "This is the book about JavaScript",
        cover: "https://handsontable.com/docs/images/examples/javascript-the-good-parts.jpg"
      },
      {
        title: "<a href='http://shop.oreilly.com/product/9780596805531.do'>JavaScript: The Definitive Guide</a>",
        description: "<em>JavaScript: The Definitive Guide</em> provides a thorough description of the core <b>JavaScript</b> language and both the legacy and standard DOMs implemented in web browsers.",
        comments: "I've never actually read it, but the <a href='http://shop.oreilly.com/product/9780596805531.do'>comments</a> are highly <strong>positive</strong>.",
        cover: "https://handsontable.com/docs/images/examples/javascript-the-definitive-guide.jpg"
      }
    ],
    container1,
    hot1;
  
  container1 = document.getElementById('example1');
  hot1 = new Handsontable(container1, {
    data: data,
    colWidths: [200, 200, 200, 80],
    colHeaders: ["Title", "Description", "Comments", "Cover"],
    columns: [
      {data: "title", renderer: "html"},
      {data: "description", renderer: "html"},
      {data: "comments", renderer: safeHtmlRenderer},
      {data: "cover", renderer: coverRenderer}
    ]
  });
  
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  function strip_tags(input, allowed) {
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
      commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
  
    // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
    allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
  
    return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
      return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
  }
  
  function safeHtmlRenderer(instance, td, row, col, prop, value, cellProperties) {
    var escaped = Handsontable.helper.stringify(value);
    escaped = strip_tags(escaped, '<em><b><strong><a><big>'); //be sure you only allow certain HTML tags to avoid XSS threats (you should also remove unwanted HTML attributes)
    td.innerHTML = escaped;
  
    return td;
  }
  
  function coverRenderer (instance, td, row, col, prop, value, cellProperties) {
    var escaped = Handsontable.helper.stringify(value),
      img;
  
    if (escaped.indexOf('http') === 0) {
      img = document.createElement('IMG');
      img.src = value;
  
      Handsontable.dom.addEvent(img, 'mousedown', function (e){
        e.preventDefault(); // prevent selection quirk
      });
  
      Handsontable.dom.empty(td);
      td.appendChild(img);
    }
    else {
      // render as text
      Handsontable.renderers.TextRenderer.apply(this, arguments);
    }
  
    return td;
  }


function customRenderer(instance, td) {
  Handsontable.renderers.TextRenderer.apply(this, arguments);

  if (isChecked) {
    td.style.backgroundColor = 'yellow';
  }
  else {
    td.style.backgroundColor = 'white';
  }

  return td;
}


/* Autocomplete */
var
  container3 = document.getElementById('example3'),
  hot3;

hot3 = new Handsontable(container3, {
  data: getCarData(),
  colHeaders: ['Car', 'Year', 'Chassis color', 'Bumper color'],
  columns: [
    {
      type: 'autocomplete',
      source: function (query, process) {
        $.ajax({
          //url: 'php/cars.php', // commented out because our website is hosted as a set of static pages
          url: 'scripts/json/autocomplete.json',
          dataType: 'json',
          data: {
            query: query
          },
          success: function (response) {
            console.log("response", response);
            //process(JSON.parse(response.data)); // JSON.parse takes string as a argument
            process(response.data);

          }
        });
      },
      strict: true
    },
    {}, // Year is a default text column
    {}, // Chassis color is a default text column
    {} // Bumper color is a default text column
  ]
});




/* actions - remove cell  */
document.addEventListener("DOMContentLoaded", function() {
  var data = [
    ['', 'Tesla', 'Nissan', 'Toyota', 'Honda', 'Mazda', 'Ford'],
    ['2017', 10, 11, 12, 13, 15, 16],
    ['2018', 10, 11, 12, 13, 15, 16],
    ['2019', 10, 11, 12, 13, 15, 16],
    ['2020', 10, 11, 12, 13, 15, 16],
    ['2021', 10, 11, 12, 13, 15, 16]
  ];
  var container = document.getElementById('example1');
  var selectFirst = document.getElementById('selectFirst');
  var removeFirstRow = document.getElementById('removeFirstRow');
  var removeFirstColumn = document.getElementById('removeFirstColumn');
  var resetState = document.getElementById('resetState');
  var hot = new Handsontable(container, {
    rowHeaders: true,
    colHeaders: true,
    data: JSON.parse(JSON.stringify(data))
  });

  Handsontable.dom.addEvent(selectFirst, 'click', function () {
    hot.selectCell(0, 0);
  });

  Handsontable.dom.addEvent(removeFirstRow, 'click', function () {
    hot.alter('remove_row', 0);
  });

  Handsontable.dom.addEvent(removeFirstColumn, 'click', function () {
    hot.alter('remove_col', 0);
  });

  Handsontable.dom.addEvent(resetState, 'click', function () {
    hot.loadData(JSON.parse(JSON.stringify(data)));
  });
});


/* copy and cut */
var container = document.getElementById('example1');
  var copyBtn = document.getElementById('copy');
  var cutBtn = document.getElementById('cut');

  var hot = new Handsontable(container, {
    rowHeaders: true,
    colHeaders: true,
    data: Handsontable.helper.createSpreadsheetData(5, 5),
    outsideClickDeselects: false,
  });

  Handsontable.dom.addEvent(copyBtn, 'mousedown', function () {
    hot.selectCell(1, 1);
  });

  Handsontable.dom.addEvent(copyBtn, 'click', function () {
    document.execCommand('copy');
  });

  Handsontable.dom.addEvent(cutBtn, 'mousedown', function () {
    hot.selectCell(1, 1);
  });

  Handsontable.dom.addEvent(cutBtn, 'click', function () {
    document.execCommand('cut');
  });



/* selecting cells */
var example1 = document.getElementById('example1');
var selectOption = document.getElementById('selectOption');
var settings1 = {
  data: Handsontable.helper.createSpreadsheetData(10, 10),
  width: 650,
  height: 272,
  colWidths: 100,
  rowHeights: 23,
  rowHeaders: true,
  colHeaders: true,
  selectionMode: 'multiple', // 'single', 'range' or 'multiple'
};
var hot1 = new Handsontable(example1, settings1);

selectOption.addEventListener('change', function(event) {
  var value = event.target.value;
  var first = value.split(' ')[0].toLowerCase();

  hot1.updateSettings({
    selectionMode: first
  });
});


/* get data */
var example2 = document.getElementById('example2');
var output = document.getElementById('output');
var getButton = document.getElementById('getButton');
var settings2 = {
  data: Handsontable.helper.createSpreadsheetData(10, 10),
  width: 650,
  height: 272,
  colWidths: 100,
  rowHeights: 23,
  rowHeaders: true,
  colHeaders: true,
  outsideClickDeselects: false,
  selectionMode: 'multiple', // 'single', 'range' or 'multiple'
};
var hot2 = new Handsontable(example2, settings2);

getButton.addEventListener('click', function(event) {
  var selected = hot2.getSelected();
  var data = [];

  for (var i = 0; i < selected.length; i += 1) {
    var item = selected[i];

    data.push(hot2.getData.apply(hot2, item));
  }

  output.value = JSON.stringify(data);
});


// change data
var example3 = document.getElementById('example3');
var buttons = document.getElementById('buttons');
var settings3 = {
  data: Handsontable.helper.createSpreadsheetData(10, 10),
  width: 650,
  height: 272,
  colWidths: 100,
  rowHeights: 23,
  rowHeaders: true,
  colHeaders: true,
  outsideClickDeselects: false,
  selectionMode: 'multiple', // 'single', 'range' or 'multiple'
};
var hot3 = new Handsontable(example3, settings3);

buttons.addEventListener('click', function(event) {
  var selected = hot3.getSelected();
  var target = event.target.id;

  for (var index = 0; index < selected.length; index += 1) {
    var item = selected[index];
    var startRow = Math.min(item[0], item[2]);
    var endRow = Math.max(item[0], item[2]);
    var startCol = Math.min(item[1], item[3]);
    var endCol = Math.max(item[1], item[3]);

    for (var rowIndex = startRow; rowIndex <= endRow; rowIndex += 1) {
      for (var columnIndex = startCol; columnIndex <= endCol; columnIndex += 1) {
        if (target === 'setButton') {
          hot3.setDataAtCell(rowIndex, columnIndex, 'data changed');
        }

        if (target === 'addButton') {
          hot3.setCellMeta(rowIndex, columnIndex, 'className', 'c-deeporange');
        }
      }
    }
  }

  hot3.render();
});