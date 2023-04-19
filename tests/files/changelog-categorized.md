# Application name

## Development changelog

### v1.1.6
- ( ) Heading: Added the heading content.

### v1.1.5
- {C} ServiceBlock: Implemented the contact options.
- (C) TextStage: Fixed the header text not being valigned middle.
- (G) Header bar: Fixed it not being shown when no teasers are present.
- {G} Header bar: Added support for integrating it elsewhere.
- ( ) Header bar: Added color and layout styles [Link label](https://mistralys.eu).
- ( ) SPINAPI Generator: Fixed boolean values in `string2php()`.
- ( ) SPINAPI Generator: Added `getDataKeyBool()`.
- ( ) SPINAPI Generator: Added support for callback arrays in the `addMethodXXX` calls.

### v1.1.4
- (C) Alert: Template colors are now used.
- (C) ServiceBox: Fixed too small paddings in the redesign.
- (C) PresenterPSA: New activation box layout in the redesign.
- {G} Typography: Increased mobile font sizes for header 2 and 3.
- ( ) Colors: Added redesign-specific colors for alerts.

### v1.1.3
- ( ) Typography: Removed unused font styles.
- (G) Typography: Unified the font styles across headers and co.
- (G) Typography: Increased the button size, now using the global base font size.
- (C) ServiceBlock: Adjusted service box header styles.
- (G) Data Grids: Fixed data grid colors, now adjustable in the Maileditor.
- {G} Data Grids: Changed default data grid header background color for the redesign.

### v1.1.2
- (C) ImageStage: Fixed some wrong spacings.
- (C) PresenterStatic: Fixed missing side margins.

### v1.1.1
- (C) MailQuota: Fixed the double bottom margin of the content.
- {G} Text: Fixed last text in body not having a bottom margin.
- ( ) StyleHelper: Added name parameter for the contentSpacing.
- (C) TextStage: Fixed no bottom margin in old layout.

### v1.1.0
- (G) Rygnarok: Updated to [v14.0.0][] for the margin handling update.
- ( ) Added the `Section` content.
- (G) Margins: Revisited all margins; Now using the new Rygnarok box model.
- (C) HandUpHandsDown: Removed the forced background, too complex to handle in combination with other contents and the content background.
- {G} Footer: The visible ID now uses the same background as the footer text.
- ( ) Margins: Removed the need entirely to manually set margins in the maileditor.
- ( ) Generator: Fixed content validation messages not bubbling up to the generator.
- ( ) Tests: Added content generation tests for the most used contents.
 
### v1.0.0
- ( ) Added this changelog.

[v14.0.0]: https://github.com/Mistralys/rygnarok/releases/tag/14.0.0