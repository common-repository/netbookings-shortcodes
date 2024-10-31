# Netbookings Shortcodes

Each shortcode in this plugin is divided into its own directory.
1. *editor-integration | Class: Netbookings_Editor_Integration*
    Responsible for adding functionality to visual editors in Wordpress that allows users to insert shortcodes.
    Supports TinyMCE.
    
2. *package-service-cards | Class: Netbookings_Cards*  
    Displays a card that contains information about a package or service using imported XML data in root directory of Wordpress installation.  
    
    Declared shortcodes:
    * [netbookings-packages]
    * [netbookings-services]

    *Compatability Notes*
    Uses the following CSS properties:
    * object-fit: https://caniuse.com/#feat=object-fit (Instead of requiring fixed image aspect ratios)
    * flex / flex-grow: https://caniuse.com/#feat=flexbox (Instead of a js based equaliser)

    Object-fit not implemented on IE, so modernizr solution is used.

    Tested on:
    * Firefox 67.0
    * Chrome 74
    * Edge 44
    * IE 10,11

3. *bathing-availability | Class: Netbookings_Bathing_Availability* 
    Shows a graph that displays bathing availability at different times.
    
    Declared shortcodes:
    * [netbookings-bathing-availability]. 
        - Takes parameters 'roomid' and 'pricinggroup'. For example, [netbookings-bathing-availability roomid=3 pricingroup=11].

    *Compatability Notes*
    Tested on:
    * Firefox 67.0
    * Chrome 74
    * Edge 44
    * IE 10,11
    
4. *general*
    Netbookings_Settings_Page: Setup for Wordpress settings page.  
    Netbookings_XML_Data: Contains XML data used to generate cards.