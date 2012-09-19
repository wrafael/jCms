$ = @jQuery


class SimpleWidget
    defaults: {}

    constructor: (el, options) ->
        @$el = $(el)
        @options = $.extend({}, @defaults, options)

        @_init()

    destroy: ->
        @_deinit();

    _init: ->
        null

    _deinit: ->
        null


SimpleWidget.register = (widget_class, widget_name) ->
    getDataKey = ->
        return "simple_widget_#{widget_name}"

    createWidget = ($el, options) ->
        data_key = getDataKey()

        $el.each(->
            widget = new widget_class(this, options)

            if not $.data(this, data_key)
                $.data(this, data_key, widget)
        )

        return $el

    destroyWidget = ($el) ->
        data_key = getDataKey()

        $el.each(->
            widget = $.data(this, data_key)

            if widget and (widget instanceof SimpleWidget)
                widget.destroy()

            $.removeData(this, data_key)
        )

    callFunction = ($el, function_name, args) ->
        result = null

        $el.each(->
            widget = $.data(this, getDataKey())

            if widget and (widget instanceof SimpleWidget)
                widget_function = widget[function_name]

                if widget_function and (typeof widget_function == 'function')
                    result = widget_function.apply(widget, args)
        )

        return result

    $.fn[widget_name] = (argument1, args...) ->
        $el = this

        if argument1 is undefined or typeof argument1 == 'object'
            options = argument1
            return createWidget($el, options)
        else if typeof argument1 == 'string' and argument1[0] != '_'
            function_name = argument1

            if function_name == 'destroy'
                return destroyWidget($el)
            else
                return callFunction($el, function_name, args)

@SimpleWidget = SimpleWidget
