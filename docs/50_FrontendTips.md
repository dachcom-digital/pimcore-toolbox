# Frontend Tips

### Gallery / Slider
Yes, there are a lot of slider out there. 
The ToolboxBundles has some references to the [slick slider](http://kenwheeler.github.io/slick/) so we'll show you some things to consider if you want to us it too:

#### Backend
There are three options you need to take care about, otherwise you'll run into some troubles if your opening your document in editmode:
```javascript
var editmode = true, //implement your js editmode resolver
    settings = {
        accessibility:  !editMode,
        rows:           0,
        infinite :      !editMode 
    };
```

### Light-Box
**Recommendation**: [LightGallery](https://github.com/sachinchoolur/lightGallery)