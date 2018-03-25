import React from "react";

export default class Header extends React.Component {
    render() {
        return (
            <nav className="navbar navbar-dark d-sm-none fixed-top">
                <a className="navbar-brand" href="#">
                    <span><i className="fa fa-bolt fa-2x" aria-hidden="true"/></span>
                    <span>Lightning.Sale</span>
                </a>
                <ul className="navbar-nav flex-row ml-auto d-flex">
                    <li className="nav-item">
                        <a href="#" className="nav-link p-2">
                            <i className="fa fa-info fa-fw"/>
                        </a>
                    </li>
                    <li className="nav-item">
                        <a href="#" className="nav-link p-2">
                            <i className="fa fa-bars fa-fw"/>
                        </a>
                    </li>
                </ul>
            </nav>
        )
    }
}