package org.swissphpfriends.gradle.helper

/**
 * Created by frank on 15/08/14.
 */
class SystemDependencies {

    public String getExecutableFromPath(String executableName) {
        String systemPath = System.getenv("PATH");
        String[] pathDirs = systemPath.split(File.pathSeparator);

        File fullyQualifiedExecutable = null;
        for (String pathDir : pathDirs) {
            File file = new File(pathDir, executableName);
            if (file.isFile()) {
                return file.getAbsolutePath();
            }
        }

        throw new FileNotFoundException('Executable file not found in path!')
    }

    public Boolean hasExecutableInPath(String executableName) {
        try {
            this.getExecutableFromPath(executableName);
            return true;
        } catch (FileNotFoundException e) {
            return false;
        }
    }
}
