VZ Range
========

A numeric range fieldtype for Expression Engine 2. Also works as a Matrix cell-type.

Template Tags
-------------

### Single Tags ###

    {range_field [precision="0"] [separator="-"] [steps="no"] [reverse="no"]}

Outputs the range, using the options specified. If you do not include any parameters, the range will look like `5-15`. The `precision` parameter overrides the global setting for the number of decimal places to use. the `separator` parameter specifies the string that will appear between each number (EE strips spaces from parameters, unfortunately, so you will need to use `&nbsp;` in place of normal spaces.) Set `steps="yes"` to output every number between the minimum and maximum. The steps will be created according to the `precision` setting. Finally, `reverse="yes"` will cause the range to be output in reverse order.

    {range_field:min}
    {range_field:max}

Output just the minimum or maximum number.


Installation
------------

Download and unzip the archive. Upload the `vz_range` folder to /system/expressionengine/third_party/.