import React from "react";

export default class Footer extends React.Component {
    render() {
        return (
            <footer style={{ position: "fixed", bottom: 0, right: "15px"}} >
                <a href="https://github.com/lightningsale/LightningSale" className="text-muted" >
                    Lightning.Sales <i className="fa fa-github"/>
                </a>&nbsp;&nbsp;&nbsp;
                <a href="https://github.com/lightningsale/LightningSale/blob/master/LICENSE" className="text-muted">
                    MIT License <i className="fa fa-copyright"/>
                </a>
            </footer>
        )
    }
}