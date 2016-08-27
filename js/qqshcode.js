var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
(function () {
    var HtmlTreating = (function () {
        function HtmlTreating() {
        }
        HtmlTreating.prototype.addElement = function (parent, hel, html) {
            var node = document.createElement(hel);
            node.innerHTML = html;
            parent.appendChild(node);
        };
        return HtmlTreating;
    }());
    var QuickQuiz = (function (_super) {
        __extends(QuickQuiz, _super);
        function QuickQuiz(dom_id) {
            _super.call(this);
            this.DOM_ID = dom_id;
            this.QElements = [];
        }
        QuickQuiz.prototype.collectAllElements = function () {
            var elms = document.querySelectorAll("div." + this.DOM_ID + " > ul li");
            var c = 1, index = 0;
            for (var eli in elms) {
                var el = elms[eli];
                if (el.childElementCount === 2) {
                    var Question = el['children'][0].innerText;
                    var Answer = el['children'][1].innerText;
                    var QE = { index: index, id: c, question: Question, answer: Answer };
                    this.QElements.push(QE);
                    el['QQE'] = QE;
                    var input_id = "id_" + this.DOM_ID + "_" + c;
                    var input_class = this.DOM_ID + "_inputclass";
                    var txt1 = "<input id='v_" + input_id + "' value='' type='text' placeholder='Your answer' />";
                    txt1 += "<div id='d_" + input_id + "' title='Get answer'>Help</div>";
                    this.addElement(el, "div", txt1);
                    var txt2 = "<span class='element-number'>" + c + "</span>";
                    this.addElement(el, "span", txt2);
                    el.addEventListener("keyup", this.verifyFields, false);
                    el.addEventListener("change", this.verifyFields, false);
                    el.addEventListener("dblclick", this.setAnswer, false);
                    var inputfield = document.getElementById("v_" + input_id);
                    var knopka = document.getElementById("d_" + input_id);
                    knopka['elementus'] = el;
                    knopka['inputfield'] = inputfield;
                    knopka.addEventListener("click", this.setAnswer2, false);
                    c++;
                }
                index++;
            }
        };
        QuickQuiz.prototype.verifyFields = function (e) {
            if (e.target.value.toLowerCase() === e.target.parentNode.parentNode.QQE.answer.toLowerCase())
                e.target.className = "input_class green";
            else
                e.target.className = "input_class red";
            if (e.target.value.toLowerCase() === "")
                e.target.className = "input_class";
        };
        QuickQuiz.prototype.setAnswer = function (e) {
            var elementus = e.target.parentNode.parentNode;
            var inputfield = e.target;
            inputfield.value = elementus.QQE.answer;
            ;
            inputfield.className = "input_class green";
        };
        QuickQuiz.prototype.setAnswer2 = function (e) {
            var elementus = e.target['elementus'];
            var inputfield = e.target['inputfield'];
            inputfield.value = elementus.QQE.answer;
            inputfield.className = "input_class green";
        };
        return QuickQuiz;
    }(HtmlTreating));
    function startWhenReady(cb) {
        var num = Math.random() + (new Date()).getTime();
        var name = 'Symbol_for_' + num;
        document[name] = Object.assign({}, { onreadystatechange: document.onreadystatechange });
        document.onreadystatechange = function () {
            if (document.readyState === "interactive") {
                if (document[name].onreadystatechange)
                    document[name].onreadystatechange();
                cb();
            }
        };
    }
    startWhenReady(function () {
        var qq = new QuickQuiz("quickquizv01");
        qq.collectAllElements();
    });
})();
