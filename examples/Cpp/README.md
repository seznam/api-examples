[C++ Requests](https://github.com/whoshuu/cpr) is a simple wrapper around [libcurl](https://curl.haxx.se/libcurl/) inspired by the excellent Python Requests project.

This is a forkable repository that handles the boilerplate of building and integrating this library into a networked application.

This project and C++ Requests both use CMake. The first step is to make sure all of the submodules are initialized:

`git submodule update --init --recursive`

Then make a build directory and do a typical CMake build from there:
`
mkdir build
cd build
cmake ..
make
`
